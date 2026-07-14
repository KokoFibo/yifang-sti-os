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

  public function DeleteBeforeSeptember()
  {
    Yfrekappresensi::whereDate('date', '<', '2025-09-01')->delete();
    $this->dispatch(
      'message',
      type: 'success',
      title: "Data seblum september 2025 berhasil dihapus."
    );
  }

  public function updateGaji()
  {
    $updated = Karyawan::whereIn('status_karyawan', ['PKWT', 'PKWTT'])
      ->where('gaji_pokok', '<=', 2300000)
      ->update([
        'gaji_pokok' => 2400000
      ]);
    dd("karyawan berhasil disesuaikan.");
    // session()->flash('success', "{$updated} karyawan berhasil disesuaikan.");

    // Tidak perlu refresh.
    // Karena render() akan dipanggil ulang otomatis.
  }

  public function render()
  {
    $data = Karyawan::whereIn('status_karyawan', ['PKWT', 'PKWTT'])
      ->where('gaji_pokok', '<=', 2300000)
      ->get();

    // dd('aman');

    return view('livewire.test', [
      'data' => $data
    ]);
  }
}
