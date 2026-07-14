<div class="container-fluid py-3">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h4 class="mb-1">
                        Penyesuaian Gaji Karyawan
                    </h4>

                    <small>
                        Gaji ≤ Rp 2.300.000 akan diubah menjadi Rp 2.400.000
                    </small>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <button class="btn btn-warning fw-bold" wire:click="updateGaji"
                        wire:confirm="Yakin ingin mengubah seluruh gaji menjadi Rp 2.400.000 ?"
                        wire:loading.attr="disabled">

                        <span wire:loading.remove>
                            Sesuaikan ke Rp 2.400.000
                        </span>

                        <span wire:loading>
                            Memproses...
                        </span>

                    </button>

                </div>

            </div>

        </div>

        <div class="card-body">

            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row mb-3">

                <div class="col-md-6">

                    <div class="alert alert-info mb-0">

                        <strong>Total Karyawan :</strong>

                        <span class="badge bg-danger">
                            {{ $data->count() }}
                        </span>

                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th class="text-end">Gaji</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($data as $item)
                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $item->nik }}</td>

                                <td>{{ $item->nama }}</td>

                                <td>

                                    <span class="badge bg-primary">
                                        {{ $item->status_karyawan }}
                                    </span>

                                </td>

                                <td class="text-end">

                                    <strong class="text-danger">

                                        Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}

                                    </strong>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center py-5">

                                    Tidak ada data.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
