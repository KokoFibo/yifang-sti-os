<?php

namespace App\Livewire;

use ZipArchive;
use Carbon\Carbon;
use App\Models\Ter;
use App\Models\User;

use App\Models\Company;
use App\Models\Jabatan;
use App\Models\Payroll;
use Livewire\Component;
use App\Models\Karyawan;
use App\Models\Tambahan;
use App\Models\Placement;
use App\Models\Requester;
use App\Models\Department;
use App\Models\Jamkerjaid;
use App\Models\Rekapbackup;
use Livewire\WithPagination;
use App\Models\Applicantfile;
use App\Models\Bonuspotongan;
use App\Models\Liburnasional;
use Illuminate\Http\Response;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;
use App\Models\Personnelrequestform;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class Test extends Component
{
  // public $saturday;
  use WithPagination;
  protected $paginationTheme = 'bootstrap';
  public $month;
  public $year;
  public $today;
  public $cx;
  public $test;


  public function mount()
  {
    $this->cx = 0;
    $this->today = now();

    $this->year = now()->year;
    $this->month = now()->month;
  }


  public function render()
  {
    // hapus data karyawan dan sisakan y=hanya yang placement STI
    dd('aman');
    $karyawans = Karyawan::where('placement_id', '!=', 104)->get();

    Karyawan::where('placement_id', '!=', 104)->delete();
    // dd('done');


    // $data = User::find("9a84287c-568f-4ace-9cce-cc30c759254f");
    // dd($data);


    $data = Yfrekappresensi::where('date', '2025-05-30')->where('user_id', 3390)->first();
    // $data = Yfrekappresensi::where('date', '2025-05-30')->where('no_scan', 'No Scan')->delete();
    // dd($data);

    // $data = Yfrekappresensi::join('karyawans', 'karyawans.id_karyawan', '=', 'yfrekappresensis.user_id')
    //   // ->where('yfrekappresensis.date', '2025-05-30')
    //   ->whereMonth('yfrekappresensis.date', 5)
    //   ->whereYear('yfrekappresensis.date', 2025)
    //   ->where('karyawans.status_karyawan', 'Blacklist')
    //   ->where('karyawans.tanggal_blacklist', '<', '2025-05-01') // ini kuncinya
    //   ->distinct()
    //   ->pluck('yfrekappresensis.user_id');

    // dd($data);


    $data = Yfrekappresensi::join('karyawans', 'karyawans.id_karyawan', '=', 'yfrekappresensis.user_id')
      ->where('yfrekappresensis.date', '2025-05-30')
      ->where(function ($query) {
        $query->where(function ($q) {
          $q->whereNull('yfrekappresensis.first_in')
            ->whereNull('yfrekappresensis.first_out');
        })->orWhere(function ($q) {
          $q->whereNull('yfrekappresensis.second_in')
            ->whereNull('yfrekappresensis.second_out');
        });
      })
      ->get();


    //51857 hari ini
    return view('livewire.test', [
      'data' => $data
    ]);
  }
}
