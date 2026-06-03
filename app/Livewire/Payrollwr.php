<?php

namespace App\Livewire;

use App\Exports\BankReportExcel;
use App\Exports\DepartmentExport;
use App\Exports\ExcelDetailReport;
use App\Exports\PayrollExport;
use App\Exports\PayrollExportFLexible;
use App\Exports\PlacementExport;
use App\Exports\PphExport;
use App\Jobs\BuildPayrollJob;
use App\Jobs\rebuildJob;
use App\Models\Company;
use App\Models\Department;
use App\Models\Jamkerjaid;
use App\Models\Karyawan;
use App\Models\Lock;
use App\Models\Payroll;
use App\Models\Placement2;
use App\Models\Placement;
use App\Models\Tambahan;
use App\Models\Yfrekappresensi;
use Aws\History;
use Carbon\Carbon;
use Google\Service\YouTube\ThirdPartyLinkStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;



class Payrollwr extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $selected_company = 0;
    public $selected_placement = 0;
    public $selected_placement2 = 0;
    public $selected_departemen = 0;
    // public $departments;
    public $search;
    public $perpage = 10;
    public $month;
    public $year;
    public $columnName = 'id_karyawan';
    public $direction = 'asc';
    public $status = 1;
    public $data_payroll;
    public $data_karyawan;
    public $cx;
    public $lock_presensi;
    public $lock_slip_gaji;
    public $lock_data;
    public $select_month, $select_year;
    public $payroll_data, $total_data;
    public   $companies, $departments, $placements, $placement2s;

    public function excelDetailReport()
    {
        $nama_file = 'OS Detail Report ' . nama_bulan($this->month) . ' ' . $this->year . '.xlsx';
        return Excel::download(new ExcelDetailReport($this->month, $this->year), $nama_file);
    }

    public function clear_lock()
    {
        clear_locks();
        delete_failed_jobs();
    }

    public function close_succesful_rebuilt()
    {
        $rebuild = Lock::find(1);
        $rebuild->rebuild_done = false;
        $rebuild->save();
    }


    public function export()
    {
        $nama_file = '';
        $nama_company = '';
        $nama_directorate = '';
        $nama_departement = '';
        $nama_placement2 = '';
        if ($this->selected_company != 0) {
            if ($this->selected_company == 0) {
                $nama_company = 'all-companies-';
            } else {
                $nama_company = 'company-' . nama_company($this->selected_company) . '-';
            }
        }
        if ($this->selected_placement != 0) {
            if ($this->selected_placement == 0) {
                $nama_directorate = 'all-directorates-';
            } else {
                $nama_directorate = 'directorate-' . nama_placement($this->selected_placement) . '-';
            }
        }
        if ($this->selected_placement2 != 0) {
            if ($this->selected_placement2 == 0) {
                $nama_placement2 = 'all-placement-';
            } else {
                $nama_placement2 = 'placement-' . nama_placement2($this->selected_placement2) . '-';
            }
        }
        if ($this->selected_departemen != 0) {
            if ($this->selected_departemen == 0) {
                $nama_departement = 'all-department-';
            } else {
                $nama_departement = 'department-' . nama_department($this->selected_departemen) . '-';
            }
        }

        if ($this->selected_company == 0 && $this->selected_placement == 0 && $this->selected_placement2 == 0 && $this->selected_departemen == 0) {

            $nama_file = 'Payroll-all-' . monthName($this->month) . '-' . $this->year . '.xlsx';
        } else {

            $nama_file = 'Payroll-' . $nama_directorate . $nama_company . $nama_departement . $nama_placement2 . monthName($this->month) . '-' . $this->year . '.xlsx';
        }

        if ($this->search != null) {
            $nama_file = 'Payroll-' . $nama_directorate . $nama_company . $nama_departement . $nama_placement2 . 'search-' . $this->search . '-' . monthName($this->month) . '-' . $this->year . '.xlsx';
        }



        return Excel::download(new PayrollExportFLexible($this->columnName, $this->direction, $this->search,  $this->selected_company, $this->selected_placement,  $this->selected_departemen, $this->status, $this->month, $this->year, $this->selected_placement2), $nama_file,);
    }


    public function bankexcel()
    {
        $query = Payroll::query()
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->orderBy('id_karyawan', 'asc');

        $nama_file = 'semua_karyawan_Bank.xlsx';

        if ($this->selected_company != 0) {

            $query->where('company_id', $this->selected_company);

            $nama_file = nama_company($this->selected_company) . '_Company_Bank.xlsx';
        } elseif ($this->selected_placement != 0) {

            $query->where('placement_id', $this->selected_placement);

            $nama_file = nama_placement($this->selected_placement) . '_Directorate_Bank.xlsx';
        } elseif ($this->selected_placement2 != 0) {

            $query->where('placement2_id', $this->selected_placement2);

            $nama_file = nama_placement($this->selected_placement2) . '_Placement2_Bank.xlsx';
        } elseif ($this->selected_departemen != 0) {

            $query->where('department_id', $this->selected_departemen);

            $nama_file = sambungKata($this->selected_departemen) . '_Department_Bank.xlsx';
        }

        $payroll = $query->get([
            'nama',
            'nama_bank',
            'nomor_rekening',
            'total',
            'company_id',
            'placement_id',
            'placement2_id',
            'department_id'
        ]);

        $nama_file = nama_file_excel(
            $nama_file,
            $this->month,
            $this->year
        );


        return Excel::download(new BankReportExcel($this->status, $this->month, $this->year, $this->selected_company, $this->selected_placement, $this->selected_placement2, $this->selected_departemen), $nama_file,);
        // return Excel::download(new BankReportExcel($payroll), $nama_file, $this->selected_company, $this->selected_placement, $this->selected_departemen);
    }

    public function showDetail($id_karyawan)
    {

        $this->data_payroll = Payroll::with('jamkerjaid')
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->where('id_karyawan', $id_karyawan)
            ->first();

        $this->data_karyawan = Karyawan::where('id_karyawan', $id_karyawan)->first();
        // dd($this->data_karyawan);
    }

    public function sortColumnName($namaKolom)
    {
        $this->columnName = $namaKolom;
        $this->direction = $this->swapDirection();
    }
    public function swapDirection()
    {
        return $this->direction === 'asc' ? 'desc' : 'asc';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadSelectMonthYear()
    {
        // ===== YEAR =====
        $this->select_year = Payroll::selectRaw('YEAR(date) as year')
            ->distinct()
            ->pluck('year')
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        $year = $this->year ?: now()->year;

        // ===== MONTH =====
        $months = Payroll::selectRaw('MONTH(date) as month')
            ->whereYear('date', $year)
            ->distinct()
            ->pluck('month');

        // 🔑 PASTI TAMBAHKAN BULAN SEKARANG JIKA TAHUN SEKARANG
        if ((int) $year === (int) now()->year) {
            // $months->push(now()->month);
            $months->push($months->max() + 1);
        }

        $this->select_month = $months
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // 🔥 INI KUNCI UTAMANYA
        if (
            empty($this->month) ||
            !in_array((int) $this->month, $this->select_month)
        ) {
            $this->month = now()->month;
        }
    }

    public function updatedYear()
    {
        $this->month = null; // reset dulu
        $this->loadSelectMonthYear();

        // $this->bulan();
        // Panggil fungsi bulan untuk memperbarui daftar bulan
    }

    public function mount()
    {

        // $this->placements = Placement::orderBy('p, lacement_name', 'ASC')->get();


        $this->placement2s = Placement2::orderBy('nama_placement', 'ASC')->get();
        $this->companies = Company::orderBy('company_name', 'ASC')->get();
        $this->departments = Department::orderBy('nama_department', 'ASC')->get();

        // Panggil fungsi bulan untuk memperbarui daftar bulan

        $data = Payroll::first();
        if (now()->day < 5) {
            $this->year =
                now()->subMonth()->year;
            $this->month =
                now()->subMonth()->month;
        } else {
            $data3 = Payroll::orderBy('date', 'desc')->first();
            // $this->year = now()->year;
            // $this->month = now()->month;
            $this->year = Carbon::parse($data3->date)->year;
            $this->month = Carbon::parse($data3->date)->month;
        }

        if ($data != null) {
            $this->data_payroll = Payroll::with('jamkerjaid')
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('id_karyawan', $data->id_karyawan)
                ->first();
            $this->data_karyawan = Karyawan::where('id_karyawan', $data->id_karyawan)->first();
        }

        $this->placements = Payroll::join('placements', 'placements.id', '=', 'payrolls.placement_id')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->select('placements.id', 'placements.placement_name')
            ->distinct()
            ->orderBy('placements.placement_name', 'ASC')
            ->get();

        $lock = Lock::find(1);
        $this->lock_presensi = $lock->presensi;
        $this->lock_slip_gaji = $lock->slip_gaji;
        $this->lock_data = $lock->data;

        $this->loadSelectMonthYear();
    }
    public function updatedLockPresensi()
    {
        // $lock=Lock::find(1);
        $lock = Lock::first();
        $lock->presensi = $this->lock_presensi;
        $lock->save();
    }
    public function updatedLockSlipGaji()
    {
        // $lock=Lock::find(1);
        $lock = Lock::first();
        $lock->slip_gaji = $this->lock_slip_gaji;
        $lock->save();
    }
    public function updatedLockData()
    {
        // $lock=Lock::find(1);
        $lock = Lock::first();
        $lock->data = $this->lock_data;
        $lock->save();
    }

    public function rebuildOptimized()
    {
        quickRebuildOptimized($this->month, $this->year);
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Data berhasil di build',
        );
    }

    public function buat_payroll($queue)
    {

        // supaya tidak dilakukan bersamaan
        if (check_absensi_kosong()) {
            clear_locks();
            // $this->dispatch('error', message: 'Masih ada data kosong di presensi');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Masih ada data kosong di presensi',
            );
            return;
        }
        $lock = Lock::find(1);
        if ($lock->build == 1) {
            // $this->dispatch('error', message: 'Mohon dicoba sebentar lagi');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Mohon dicoba sebentar lagi',
            );
            return;
        } else {
            $lock->build = 1;
            $lock->save();
        }

        // hapus ini kalau sudah kelar develop
        $lock->build = 1;
        $lock->save();

        $startTime = microtime(true);
        if ($queue == 'noQueue') {
            $result  = build_payroll_os($this->month, $this->year);
        } else {
            $lock = Lock::find(1);
            $lock->rebuild_done = 2;
            $lock->save();
            dispatch(new rebuildJob($this->month, $this->year));
            $result = 1;
        }


        if ($result == 0) {
            // $this->dispatch('error', message: 'Data Presensi tidak ada');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Data Presensi tidak ada',
            );
        } else {
            if ($queue == 'noQueue') {
                $this->dispatch(
                    'message',
                    type: 'success',
                    title: 'Data Payroll Karyawan Sudah di Built ( ' . number_format((microtime(true) - $startTime), 2) . ' seconds )',
                );
            }
        }


        $lock->build = 0;
        $lock->save();
        $this->mount();
    }



    // ok3
    public function bonus_potongan()
    {
        $bonus = 0;
        $potongaan = 0;
        $all_bonus = 0;
        $all_potongan = 0;
        $tambahan = Tambahan::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        foreach ($tambahan as $d) {
            $all_bonus = $d->uang_makan + $d->bonus_lain;
            $all_potongan = $d->baju_esd + $d->gelas + $d->sandal + $d->seragam + $d->sport_bra + $d->hijab_instan + $d->id_card_hilang + $d->masker_hijau + $d->potongan_lain;
            $id_payroll = Payroll::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('id_karyawan', $d->user_id)
                ->first();
            if ($id_payroll != null) {
                $payroll = Payroll::find($id_payroll->id);
                $payroll->bonus1x = $payroll->bonus1x + $all_bonus;
                $payroll->potongan1x = $payroll->potongan1x + $all_potongan;
                $payroll->total = $payroll->total + $all_bonus - $all_potongan;
                $payroll->save();
            }
        }

        $this->dispatch(
            'message',
            type: 'success',
            title: 'Bonus dan Potangan added',
        );
    }

    public function getPayrollQuery($statuses, $search = null, $placement_id = null, $placement2_id = null, $company_id = null, $department_id = null)
    {
        return Payroll::query()

            ->whereIn('status_karyawan', $statuses)
            ->when($search, function ($query) use ($search) {
                $query
                    // ->where('id_karyawan', 'LIKE', '%' . trim($search) . '%')
                    ->where('id_karyawan',  trim($search))
                    ->orWhere('nama', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('jabatan_id', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('company_id', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('department_id', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('metode_penggajian', 'LIKE', '%' . trim($search) . '%')
                    ->orWhere('status_karyawan', 'LIKE', '%' . trim($search) . '%');
            })
            ->when($placement_id, function ($query) use ($placement_id) {
                $query->where('placement_id', $placement_id);
            })
            ->when($placement2_id, function ($query) use ($placement2_id) {
                $query->where('placement2_id', $placement2_id);
            })
            ->when($company_id, function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            })
            ->when($department_id, function ($query) use ($department_id) {
                $query->where('department_id', $department_id);
            })

            ->orderBy($this->columnName, $this->direction);
    }

    public function updatedSelectedCompany()
    {
        $department_ids = Payroll::where('company_id', $this->selected_company)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->distinct()
            ->pluck('department_id');

        $this->departments = Department::whereIn('id', $department_ids)
            ->orderBy('nama_department', 'ASC')
            ->get();
    }
    public function updatedSelectedPlacement()
    {
        // $this->placements = Placement::orderBy('placement_name', 'ASC')->get();
        $this->placements = Payroll::join('placements', 'placements.id', '=', 'payrolls.placement_id')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->select('placements.id', 'placements.placement_name')
            ->distinct()
            ->orderBy('placements.placement_name', 'ASC')
            ->get();


        $company_ids = Payroll::where('placement_id', $this->selected_placement)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->distinct()
            ->pluck('company_id');

        $this->companies = Company::whereIn('id', $company_ids)
            ->orderBy('company_name', 'ASC')
            ->get();

        $department_ids = Payroll::where('placement_id', $this->selected_placement)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->distinct()
            ->pluck('department_id');

        $this->departments = Department::whereIn('id', $department_ids)
            ->orderBy('nama_department', 'ASC')
            ->get();
    }

    public function updatedSelectedPlacement2()
    {
        $this->placement2s = Placement2::orderBy('nama_placement', 'ASC')->get();

        $payroll = Payroll::where('placement_id', $this->selected_placement2)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year);

        $this->companies = Company::whereIn(
            'id',
            (clone $payroll)->distinct()->pluck('company_id')
        )
            ->orderBy('company_name')
            ->get();

        $this->departments = Department::whereIn(
            'id',
            (clone $payroll)->distinct()->pluck('department_id')
        )
            ->orderBy('nama_department')
            ->get();

        $this->placements = Placement::whereIn(
            'id',
            (clone $payroll)->whereNotNull('placement2_id')->distinct()->pluck('placement2_id')
        )
            ->orderBy('placement_name')
            ->get();

        $this->selected_placement = 0;
    }


    private function applyCommonFilters($query)
    {
        return $query
            ->when($this->selected_company != 0, fn($q) => $q->where('company_id', $this->selected_company))
            ->when($this->selected_placement != 0, fn($q) => $q->where('placement_id', $this->selected_placement))
            ->when($this->selected_placement2 != 0, fn($q) => $q->where('placement2_id', $this->selected_placement2))
            ->when($this->selected_departemen != 0, fn($q) => $q->where('department_id', $this->selected_departemen))
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year);
    }

    public function render()
    {

        $this->cx++;



        $months = Payroll::select(DB::raw('MONTH(date) as month'))
            ->whereYear('date', $this->year)
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('month')
            ->toArray();




        if ($this->status == 1) {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'];
        } elseif ($this->status == 2) {
            $statuses = ['Blacklist'];
        } else {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned', 'Blacklist'];
        }

        $total = $this->applyCommonFilters(
            Payroll::whereIn('status_karyawan', $statuses)
        )->sum('total');

        $payroll = $this->applyCommonFilters(
            $this->getPayrollQuery($statuses, $this->search)
        )->orderBy($this->columnName, $this->direction)
            ->paginate($this->perpage);



        $tgl = Payroll::whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->select('created_at')->first();
        if ($tgl != null) {
            $last_build = Carbon::parse($tgl->created_at)->diffForHumans();
        } else {
            $last_build = 0;
        }

        $data_kosong = Jamkerjaid::count();


        $this->cx++;


        // dd($departments->all());


        return view('livewire.payrollwr', [
            'payroll' => $payroll,
            'total' => $total,
            'last_build' => $last_build,
            'data_kosong' => $data_kosong,

        ]);
    }
}
