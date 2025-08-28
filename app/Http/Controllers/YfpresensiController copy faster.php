<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lock;
use App\Models\User;
use App\Models\Employee;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Department;
use App\Models\Jamkerjaid;
use App\Models\Yfpresensi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class YfpresensiController extends Controller
{

    public function compare(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);

        $importedData = $spreadsheet->getActiveSheet();
        $row_limit = $importedData->getHighestDataRow();
        $tgl = trim(explode('~', $importedData->getCell('A2')->getValue())[1]);
        $tgl1 = trim(explode('~', $importedData->getCell('A2')->getValue())[0]);
        $tgl2 = trim(explode(':', $tgl1)[1]);
        if ($tgl != $tgl2) {
            clear_locks();
            return back()->with('error', 'Gagal Upload Tanggal harus dihari yang sama');
        }
        $user_id = '';
        $name = '';
        $department = '';
        $late = null;
        $no_scan = null;
        $tgl_delete = null;

        // check Tanggal apakah ada yang sama
        $tgl_sama = DB::table('yfrekappresensis')
            ->where('date', $tgl)
            ->get('user_id');

        // check apakah tgl fresh

        $freshDate = DB::table('yfrekappresensis')
            ->where('date', $tgl)->first();
        if ($freshDate != null) {
            $cx = 0;
            $datasama = [];
            for ($i = 5; $i <= $row_limit; $i++) {

                if ($importedData->getCell('A' . $i)->getValue() != '') {

                    $user_id = $importedData->getCell('A' . $i)->getValue();
                    $time = $importedData->getCell('D' . $i)->getValue();

                    if ($tgl_sama->isNotEmpty()) {

                        foreach ($tgl_sama as $data) {
                            $cx++;
                            if ($user_id == $data->user_id && $time != '') {
                                $datasama[] = [
                                    'user_id' => $user_id,
                                ];
                            }
                        }
                    }
                }
            }
            if (count($datasama) > 0) {
                // Generate formatted user IDs
                $formattedIds = [];
                foreach ($datasama as $d) {
                    $formattedIds[] = $d['user_id'];
                }
                $msg = 'Data tidak bisa diupload karena terdapat user id yang sama: ' . implode(', ', $formattedIds);

                return back()->with('error', $msg);
            }
        }

        return back()->with('success', 'Tidak ada data duplikat');


        // if (collect($datasama)->count() > 0) {
        //  $userid = [];
        //     foreach ($datasama as $d) {
        //         $userid = $userid + $userid.',';
        //     }

        //     $msg = ''Data Tidak bisa di upload karena terdapat user id yang sama :'
        //     return back()->with('error', 'Data Tidak bisa di upload karena terdapat user id yang sama ' . $data->user_id);
        // } else {

        //     dd('aman');
        // }

        // try {
        //     foreach (array_chunk($Yfpresensidata, 200) as $item) {
        //         Yfpresensi::insert($item);
        //     }
        // } catch (\Exception $e) {
        //     clear_locks();
        //     return back()->with('error', 'Gagal Upload Format tanggal tidak sesuai');
        // }



    }

    public function deleteByPabrik(Request $request)
    {
        dd('masih ada bugs karena jika hapus maka akan kehapus semua');
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);

        $importedData = $spreadsheet->getActiveSheet();
        $row_limit = $importedData->getHighestDataRow();

        $tgl = trim(explode('~', $importedData->getCell('A2')->getValue())[1]);
        $tgl1 = trim(explode('~', $importedData->getCell('A2')->getValue())[0]);
        $tgl2 = trim(explode(':', $tgl1)[1]);
        if ($tgl != $tgl2) {
            clear_locks();
            return back()->with('error', 'Gagal Upload Tanggal harus dihari yang sama');
        }
        $user_id = '';
        $name = '';
        $department = '';
        $late = null;
        $no_scan = null;
        $tgl_delete = null;

        // check Tanggal apakah ada yang sama
        $tgl_sama = DB::table('yfrekappresensis')
            ->where('date', $tgl)
            ->get('user_id');



        for ($i = 5; $i <= $row_limit; $i++) {

            if ($importedData->getCell('A' . $i)->getValue() != '') {
                $user_id = $importedData->getCell('A' . $i)->getValue();
                $name = $importedData->getCell('B' . $i)->getValue();
                if ($tgl_sama->isNotEmpty()) {
                    foreach ($tgl_sama as $data) {
                        if ($user_id == $data->user_id) {
                            clear_locks();
                            return back()->with('error', 'Data presensi in belum pernah di upload');
                        }
                    }
                }
            }

            if ($importedData->getCell('D' . $i)->getValue() != '') {
                $time = date('H:i', strtotime($importedData->getCell('D' . $i)->getValue()));
                if (strpos($importedData->getCell('D' . $i)->getValue(), '+') !== false) {
                    $str = str_replace('+', '', $importedData->getCell('D' . $i)->getValue());
                    $time = date('H:i', strtotime($str));
                }



                //  pakai Chunk
                $Yfpresensidata[] = [
                    'user_id' => $user_id,
                    // 'name' => $name,
                    'date' => $tgl,
                    'time' => $time,
                    'day_number' => date('w', strtotime($tgl)),
                ];
            }
        }

        // try {
        //     foreach (array_chunk($Yfpresensidata, 200) as $item) {
        //         Yfpresensi::insert($item);
        //     }
        // } catch (\Exception $e) {
        //     clear_locks();
        //     return back()->with('error', 'Gagal Upload Format tanggal tidak sesuai');
        // }


        $unique = collect($Yfpresensidata)->unique('user_id');
        $cx = 0;
        foreach ($unique as $d) {
            try {
                $data = Yfrekappresensi::where('user_id', $d['user_id'])->where('date', $d['date'])->delete();
                $cx++;
            } catch (\Exception $e) {
                // Handle the exception, e.g., log it or notify someone
                // For example:
                dd($e->getMessage());
            }
        }
        // dd('Data Deleted: ', $cx);
        return back()->with('error', 'Data Deleted: ' . $cx);
    }

    public function generateUsers()
    {

        $karyawan = Karyawan::get(['nama', 'email', 'id_karyawan', 'tanggal_lahir']);
        // $userArray = [];
        foreach ($karyawan as $item) {

            User::create([
                'name' => titleCase($item->nama),
                'email' =>  trim($item->email),
                'username' => trim($item->id_karyawan),
                'role' => 1,
                'remember_token' => Str::random(10),
                'password' => Hash::make(generatePassword($item->tanggal_lahir)),
            ]);
        }

        dd('Done');
    }

    public function deleteJamKerja()
    {
        Jamkerjaid::query()->truncate();
        return back()->with('success', 'Data Jam Kerja telah berhasil di delete');
    }

    public function deleteNoScan()
    {
        Yfrekappresensi::where('no_scan', 'No Scan')->delete();
        return back()->with('success', 'Data No scan telah berhasil di delete');
    }

    public function deletepresensi_no_use()
    {
        Yfpresensi::query()->truncate();
        Yfrekappresensi::query()->truncate();
        // Presensi::query()->truncate();
        return back()->with('success', 'Data Presensi telah berhasil di delete');
    }

    public function store(Request $request)
    {
        $lock = Lock::find(1);
        if ($lock->upload) {
            $lock->upload = false;
            $lock->save();
            return back()->with('error', 'Mohon dicoba sebentar lagi ya');
        } else {
            $lock->upload = true;
            $lock->save();
        }

        Yfpresensi::query()->truncate();

        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);

        $importedData = $spreadsheet->getActiveSheet();
        $row_limit = $importedData->getHighestDataRow();

        $tgl = trim(explode('~', $importedData->getCell('A2')->getValue())[1]);
        $tgl1 = trim(explode('~', $importedData->getCell('A2')->getValue())[0]);
        $tgl2 = trim(explode(':', $tgl1)[1]);

        if ($tgl != $tgl2) {
            clear_locks();
            return back()->with('error', 'Gagal Upload Tanggal harus dihari yang sama');
        }

        $tgl_sama = DB::table('yfrekappresensis')->where('date', $tgl)->get('user_id');

        $Yfpresensidata = [];

        for ($i = 5; $i <= $row_limit; $i++) {
            if ($importedData->getCell('A' . $i)->getValue() != '') {
                $user_id = $importedData->getCell('A' . $i)->getValue();
                $time = $importedData->getCell('D' . $i)->getValue();

                if ($tgl_sama->isNotEmpty()) {
                    foreach ($tgl_sama as $data) {
                        if ($user_id == $data->user_id && $time != '') {
                            clear_locks();
                            return back()->with('error', 'Gagal Upload, User ID kembar : ' . $data->user_id);
                        }
                    }
                }

                $time = date('H:i', strtotime($time));
                if (strpos($time, '+') !== false) {
                    $time = date('H:i', strtotime(str_replace('+', '', $time)));
                }

                // Prepare the data for bulk insertion
                $Yfpresensidata[] = [
                    'user_id' => $user_id,
                    'date' => $tgl,
                    'time' => $time,
                    'day_number' => date('w', strtotime($tgl)),
                ];
            }
        }

        try {
            foreach (array_chunk($Yfpresensidata, 200) as $item) {
                Yfpresensi::insert($item);
            }
        } catch (\Exception $e) {
            clear_locks();
            return back()->with('error', 'Gagal Upload Format tanggal tidak sesuai');
        }

        // Clean-up entries that have no valid employee
        $data_yfpresensi = Yfpresensi::select('user_id')->get();
        foreach ($data_yfpresensi as $dyf) {
            $finddata = Karyawan::where('id_karyawan', $dyf->user_id)->exists();
            if (!$finddata) {
                Yfpresensi::where('user_id', $dyf->user_id)->delete();
            }
        }

        $jumlahKaryawanHadir = DB::table('yfpresensis')
            ->distinct('user_id')
            ->count('user_id');

        $karyawanHadir = DB::table('yfpresensis')
            ->select('user_id', 'date')
            ->distinct()
            ->get();

        foreach ($karyawanHadir as $kh) {
            $first_in = $first_out = $second_in = $second_out = $overtime_in = $overtime_out = null;
            $shift = $this->determineShift($kh->date, $kh->user_id);

            // Calculate lateness or no scan
            $late = $this->calculateLate($first_in, $first_out, $second_in, $second_out, $overtime_in, $overtime_out, $shift, $kh->date, $kh->user_id);

            // Insert the data into the recap table
            $dataKaryawan = Karyawan::where('id_karyawan', $kh->user_id)->first();
            $id_karyawan = $dataKaryawan ? $dataKaryawan->id : 'kosong';

            Yfrekappresensi::create([
                'user_id' => $kh->user_id,
                'karyawan_id' => $id_karyawan,
                'date' => $kh->date,
                'first_in' => $first_in,
                'first_out' => $first_out,
                'second_in' => $second_in,
                'second_out' => $second_out,
                'overtime_in' => $overtime_in,
                'overtime_out' => $overtime_out,
                'shift' => $shift,
                'late' => $late,
                'no_scan' => $late ? null : $this->noScan($first_in, $first_out, $second_in, $second_out, $overtime_in, $overtime_out),
            ]);
        }

        Yfpresensi::query()->truncate();
        clear_locks();
        return back()->with('info', 'Berhasil Import : ' . $jumlahKaryawanHadir . ' data');
    }

    /**
     * Determine the shift based on the date and user_id.
     *
     * @param string $date
     * @param string $user_id
     * @return string
     */
    private function determineShift($date, $user_id)
    {
        $is_saturday = is_saturday($date);
        $is_puasa = is_puasa($date);
        $placement = get_placement($user_id);

        if ($is_puasa && $placement == 'YCME') {
            // Specific logic for YCME during puasa
            return Carbon::parse($date)->betweenIncluded('05:30', '15:00') ? 'Pagi' : 'Malam';
        }

        if ($is_saturday) {
            return Carbon::parse($date)->betweenIncluded('05:30', '13:00') ? 'Pagi' : 'Malam';
        }

        return Carbon::parse($date)->betweenIncluded('05:30', '15:00') ? 'Pagi' : 'Malam';
    }

    /**
     * Calculate the late or no scan status.
     *
     * @param string|null $first_in
     * @param string|null $first_out
     * @param string|null $second_in
     * @param string|null $second_out
     * @param string|null $overtime_in
     * @param string|null $overtime_out
     * @param string $shift
     * @param string $date
     * @param string $user_id
     * @return string|null
     */
    private function calculateLate($first_in, $first_out, $second_in, $second_out, $overtime_in, $overtime_out, $shift, $date, $user_id)
    {
        $late = late_check_detail($first_in, $first_out, $second_in, $second_out, $overtime_in, $shift, $date, $user_id);

        if ($this->noScan($first_in, $first_out, $second_in, $second_out, $overtime_in, $overtime_out)) {
            return null;
        }

        return $late;
    }

    /**
     * Determine if there is any scan missing.
     *
     * @param string|null $first_in
     * @param string|null $first_out
     * @param string|null $second_in
     * @param string|null $second_out
     * @param string|null $overtime_in
     * @param string|null $overtime_out
     * @return bool
     */
    private function noScan($first_in, $first_out, $second_in, $second_out, $overtime_in, $overtime_out)
    {
        return $first_in === null || $first_out === null || $second_in === null || $second_out === null;
    }

    public function index()
    {
        return view('yfpresensi.index');
    }
}
