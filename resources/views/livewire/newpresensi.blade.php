<div class="m-2">
    <div class="card shadow-sm">
        <div class="card-header {{ $is_hari_libur_nasional || $is_sunday ? 'bg-success' : 'bg-primary' }} text-white">
            <h4 class="fs-5">
                Data Presensi
            </h4>
            <h5 class="fs-6">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}
                {{ $is_hari_libur_nasional ? '( Hari Libur Nasional )' : '' }}
            </h5>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2 mt-3 my-2 mx-3">
            <!-- ⏪ Navigasi tanggal -->
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="prevDate">
                &larr;
            </button>

            <input type="date" wire:model.live="tanggal" class="form-control form-control-sm w-auto text-center" />

            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="nextDate">
                &rarr;
            </button>
            @if ($totalNoScanDaily > 0)
                <p class="mb-0 text-sm m-3">No Scan : {{ $noScan }} of {{ $totalNoScanDaily }}</p>
            @endif

            @if ($absensiKosong > 0)
                <button wire:click="filterKosong" class="btn btn-sm btn-danger" type="button">{{ __('Kosong : ') }}
                    {{ $absensiKosong }}</button>
                <button wire:click="delete_presensi_kosong" wire:confirm="Yakin mau di delete semua?"
                    class="btn btn-sm btn-danger" type="button">{{ __('Delete Semua Presensi Kosong') }}</button>
            @endif
            @if ($totalNoScan > 0)
                <button wire:click="filterNoScan" class="btn btn-sm btn-warning"
                    type="button">{{ __('Total No Scan :') }} {{ $totalNoScan }}</button>
            @else
            @endif
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-2 mx-3">
            <!-- 🔍 Search & Filter -->
            <div class="d-flex align-items-center flex-wrap gap-2">
                <!-- 🔍 Input pencarian -->
                <input type="text" wire:model.debounce.500ms.live="search" placeholder="Cari nama / user ID"
                    class="form-control form-control-sm" style="width: 200px;" />

                <!-- 🏢 Filter Placement -->
                <select wire:model.live="placementFilter" wire:change="reload_placement_jabatan"
                    class="form-select form-select-sm" style="width: 180px;">
                    <option value="">Semua Directorate</option>
                    @foreach ($placements as $p)
                        <option value="{{ $p->id }}">{{ $p->placement_name }}</option>
                    @endforeach
                </select>

                <!-- 👔 Filter Jabatan -->
                <select wire:model.live="jabatanFilter" class="form-select form-select-sm" style="width: 180px;">
                    <option value="">Semua Jabatan</option>
                    @foreach ($jabatans as $j)
                        <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                    @endforeach
                </select>

                <!-- 👔Rows perpage -->
                <select wire:model.live="rowsPerPage" class="form-select form-select-sm" style="width: 180px;">
                    <option value="10">10 Rows perpage</option>
                    <option value="15">15 Rows perpage</option>
                    <option value="20">20 Rows perpage</option>
                    <option value="25">25 Rows perpage</option>
                </select>
            </div>
            <a href="/yfupload">
                <button class="btn btn-sm btn-success">Upload Presensi</button>
            </a>
        </div>
        @if (auth()->user()->role == 8)
            <div class="d-flex align-items-center flex-wrap gap-2 mt-2 mx-3">
                <a href="/deleteduplicatepresensi">
                    <button class="btn btn-sm btn-success nightowl-daylight">{{ __('Cek Duplikat') }}</button></a>
                <a onclick="return confirm('Mau delete Tgl Presensi?')" href="/yfdeletetanggalpresensiwr">
                    <button
                        class="btn btn-sm btn-warning nightowl-daylight">{{ __('Delete Tgl Presensi') }}</button></a>
                <a href="/addpresensi">
                    <button class="btn btn-sm btn-success nightowl-daylight">{{ __('Add Presensi') }}</button></a>
            </div>
        @endif

        <style>
            .table td,
            .table th {
                vertical-align: middle !important;
            }

            .btn-xs {
                padding: 0.15rem 0.5rem;
                font-size: 1 rem;
                line-height: 1.5;
            }
        </style>


        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle text-nowrap text-sm">
                    <thead class="table-light text-center">

                        <tr>
                            @php
                                $columns = [
                                    'user_id' => '',
                                    'user_id' => 'User ID',
                                    'nama' => 'Nama',
                                    'metode_penggajian' => 'Metode Penggajian',
                                    'placement_id' => 'Directorate',
                                    'jabatan_id' => 'Jabatan',
                                    'first_in' => 'First In',
                                    'first_out' => 'First Out',
                                    'second_in' => 'Second In',
                                    'second_out' => 'Second Out',
                                    'overtime_in' => 'Overtime In',
                                    'overtime_out' => 'Overtime Out',
                                    'total_jam_kerja' => 'Total Jam Kerja',
                                    'total_hari_kerja' => 'Total Hari Kerja',
                                    'total_jam_lembur' => 'Total Jam Lembur',
                                    'total_jam_kerja_libur' => 'Total Jam Kerja Libur',
                                    'late' => 'Late',
                                    'no_scan' => 'No Scan',
                                    'shift' => 'Shift',
                                    'shift_malam' => 'Tambahan Shift Malam',
                                ];
                            @endphp
                            <th>action
                            </th>
                            @foreach ($columns as $field => $label)
                                {{-- hanya tampilkan kolom total_jam_kerja_libur kalau role == 8 --}}
                                @if ($field === 'total_jam_kerja_libur' && Auth::user()->role != 8)
                                    @continue
                                @endif
                                <th wire:click="sortBy('{{ $field }}')" style="cursor:pointer;">
                                    {{ $label }}
                                    @if ($sortField === $field)
                                        @if ($sortDirection === 'asc')
                                            <i class="bi bi-arrow-up"></i>
                                        @else
                                            <i class="bi bi-arrow-down"></i>
                                        @endif
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @foreach ($datas as $data)
                            <tr class="
        @if ($data->no_scan > 0) table-warning @endif
    ">
                                <td>
                                    @if (!$is_presensi_locked || Auth::user()->role == 8)
                                        <button class="btn btn-secondary btn-xs"
                                            wire:click="edit({{ $data->id }})"><i
                                                class="fa-regular
                                        fa-pen-to-square fa-xs"></i></button>
                                    @endif

                                    <button type="button" class="btn btn-secondary btn-xs"
                                        wire:click="showDetail_baru({{ $data->user_id }})" data-bs-toggle="modal"
                                        data-bs-target="#update-form-modal"><i
                                            class="fa-solid fa-magnifying-glass fa-xs"></i></button>

                                    @if (Auth::user()->role == 8)
                                        <button class="btn btn-danger btn-xs" wire:click="delete({{ $data->id }})"
                                            wire:confirm.prompt="Yakin mau di delete?\n\nKetik DELETE untuk konfirmasi|DELETE"><i
                                                class="fa-solid fa-trash-can confirm-delete fa-xs"></i></button>
                                    @endif
                                </td>
                                <td>{{ $data->user_id }}</td>
                                <td>{{ $data->nama }}</td>
                                <td>{{ $data->metode_penggajian }}</td>
                                <td>{{ nama_placement($data->placement_id ?? '-') }}</td>
                                <td>{{ nama_jabatan($data->jabatan_id ?? '-') }}</td>

                                <td>{{ $data->first_in ? \Carbon\Carbon::parse($data->first_in)->format('H:i') : '-' }}
                                </td>
                                <td>{{ $data->first_out ? \Carbon\Carbon::parse($data->first_out)->format('H:i') : '-' }}
                                </td>
                                <td>{{ $data->second_in ? \Carbon\Carbon::parse($data->second_in)->format('H:i') : '-' }}
                                </td>
                                <td>{{ $data->second_out ? \Carbon\Carbon::parse($data->second_out)->format('H:i') : '-' }}
                                </td>
                                <td>{{ $data->overtime_in ? \Carbon\Carbon::parse($data->overtime_in)->format('H:i') : '-' }}
                                </td>
                                <td>{{ $data->overtime_out ? \Carbon\Carbon::parse($data->overtime_out)->format('H:i') : '-' }}
                                </td>

                                <td>{{ $data->total_jam_kerja }}</td>
                                <td>{{ $data->total_hari_kerja }}</td>
                                <td>{{ $data->total_jam_lembur }}</td>
                                @if (Auth::user()->role == 8)
                                    <td>{{ $data->total_jam_kerja_libur }}</td>
                                @endif
                                <td>{{ $data->late }}</td>
                                <td>{{ $data->no_scan }}</td>
                                <td>{{ $data->shift }}</td>
                                <td>{{ $data->shift_malam }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <div class="px-3">
                    {{ $datas->onEachSide(0)->links() }}
                </div>
            </div>

        </div>
    </div>
    <!-- Modal Edit -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" role="dialog"
        aria-labelledby="editModalLabel" aria-hidden="true">
        {{-- <div class="modal-dialog modal-dialog-centered " role="document"> --}}
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editModalLabel">Edit Jam Kerja</h5>
                        <h6>{{ $show_name }} ({{ $show_id }})</h6>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        wire:click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>First In</label>
                            <input type="time" class="form-control" wire:model.defer="first_in" step="60"
                                placeholder="--:--" onfocus="this.showPicker()"
                                onkeydown="if(event.key==='Delete'||event.key==='Backspace'){ this.value=''; @this.set('first_in', null) }">
                        </div>

                        <div class="form-group col-md-6">
                            <label>First Out</label>
                            <input type="time" class="form-control" wire:model.defer="first_out" step="60"
                                placeholder="--:--" onfocus="this.showPicker()"
                                onkeydown="if(event.key==='Delete'||event.key==='Backspace'){ this.value=''; @this.set('first_out', null) }">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Second In</label>
                            <input type="time" class="form-control" wire:model.defer="second_in" step="60"
                                placeholder="--:--" onfocus="this.showPicker()"
                                onkeydown="if(event.key==='Delete'||event.key==='Backspace'){ this.value=''; @this.set('second_in', null) }">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Second Out</label>
                            <input type="time" class="form-control" wire:model.defer="second_out" step="60"
                                placeholder="--:--" onfocus="this.showPicker()"
                                onkeydown="if(event.key==='Delete'||event.key==='Backspace'){ this.value=''; @this.set('second_out', null) }">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Overtime In</label>
                            <input type="time" class="form-control" wire:model.defer="overtime_in" step="60"
                                placeholder="--:--" onfocus="this.showPicker()"
                                onkeydown="if(event.key==='Delete'||event.key==='Backspace'){ this.value=''; @this.set('overtime_in', null) }">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Overtime Out</label>
                            <input type="time" class="form-control" wire:model.defer="overtime_out"
                                step="60" placeholder="--:--" onfocus="this.showPicker()"
                                onkeydown="if(event.key==='Delete'||event.key==='Backspace'){ this.value=''; @this.set('overtime_out', null) }">
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Tutup</button>
                    <button type="button" class="btn btn-success" wire:click="update">Simpan</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        window.addEventListener('show-edit-modal', () => {
            $('#editModal').modal('show');
        });

        window.addEventListener('hide-edit-modal', () => {
            $('#editModal').modal('hide');
        });
    </script>
    <script>
        document.addEventListener('keydown', function(event) {
            // Panah kiri
            if (event.key === 'ArrowLeft') {
                @this.call('prevDate'); // panggil method Livewire
            }

            // Panah kanan
            if (event.key === 'ArrowRight') {
                @this.call('nextDate');
            }
        });
    </script>

    @include('modals.presensi')

</div>
