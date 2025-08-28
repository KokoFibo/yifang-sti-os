<div>
    <div class="container">
        <div class="card">
            <div class="card-header  bg-success nightowl-daylight">
                Bonus dan Potongan
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-column flex-xl-row">
                    <div class="mb-3 col-12 col-xl-2">
                        <label for="user_id" class="form-label">ID Karyawan</label>
                        <input wire:model.live="user_id" type="numeric" class="form-control" id="user_id">
                    </div>
                    <div class="mb-3 col-12 col-xl-3">
                        <label for="nama_karyawan" class="form-label">{{ __('Nama Karyawan') }}</label>
                        <input wire:model.live="nama_karyawan" type="text" class="form-control" id="nama_karyawan"
                            disabled>
                    </div>


                </div>
                <div class="mb-3 col-12 col-xl-2">
                    <label class="form-label">{{ __('Tanggal') }} (mm/dd/yyyy)</label>
                    <input wire:model="tanggal" class="form-control" type="date">
                </div>

                <div class="card mt-lg-3 mt-2">
                    <div class="card-header bg-success nightowl-daylight">
                        <h5>Bonus</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-xl-row flex-column gap-xl-3 gap-2">

                            <div class="mb-3">
                                <label class="form-label">{{ __('Uang Makan') }}</label>
                                {{-- <input wire:model="uang_makan" type="text" type-currency="IDR" class="form-control"> --}}
                                <input wire:model="uang_makan" type="text" type-currency="IDR" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Bonus Lain') }}</label>
                                <input wire:model="bonus_lain" type="text" type-currency="IDR" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-lg-3 mt-2">
                    <div class="card-header bg-info nightowl-daylight">
                        <h5>Potongan</h5>
                    </div>
                    <div class="card-body ">
                        <div class="d-flex flex-column flex-lg-row  gap-2 justify-content-center">
                            <div class="mb-3 col-lg-3 col-12">
                                <label class="form-label">{{ __('Baju ESD') }}</label>
                                <input wire:model="baju_esd" type="text" type-currency="IDR" class="form-control">
                            </div>

                            <div class="mb-3 col-lg-3 col-12">
                                <label class="form-label">{{ __('Gelas') }}</label>
                                <input wire:model="gelas" type="text" type-currency="IDR" class="form-control">
                            </div>

                            <div class="mb-3 col-lg-3 col-12">
                                <label class="form-label">{{ __('Sandal') }}</label>
                                <input wire:model="sandal" type="text" type-currency="IDR" class="form-control">
                            </div>

                            <div class="mb-3 col-lg-3 col-12">
                                <label class="form-label">{{ __('Seragam') }}</label>
                                <input wire:model="seragam" type="text" type-currency="IDR" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-lg-row  gap-2 justify-content-between">

                            <div class="mb-3 col-lg-2 col-12">
                                <label class="form-label">{{ __('Sport Bra') }}</label>
                                <input wire:model="sport_bra" type="text" type-currency="IDR" class="form-control">

                            </div>


                            <div class="mb-3 col-lg-2 col-12">
                                <label class="form-label">{{ __('Hijab Instan') }}</label>
                                <input wire:model="hijab_instan" type="text" type-currency="IDR"
                                    class="form-control">

                            </div>


                            <div class="mb-3 col-lg-2 col-12">
                                <label class="form-label">{{ __('ID Card Hilang') }}</label>
                                <input wire:model="id_card_hilang" type="text" type-currency="IDR"
                                    class="form-control">
                            </div>



                            <div class="mb-3 col-lg-2 col-12">
                                <label class="form-label">{{ __('Masker Hijau') }}</label>
                                <input wire:model="masker_hijau" type="text" type-currency="IDR"
                                    class="form-control">
                            </div>


                            <div class="mb-3 col-lg-2 col-12">
                                <label class="form-label">{{ __('Potongan Lain') }}</label>
                                <input wire:model="potongan_lain" type="text" type-currency="IDR"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    {{-- </div> --}}
                </div>
                <div class="d-flex gap-5 mt-3">
                    <button wire:click="save" class="btn btn-success nightowl-daylight">{{ __('Save') }}</button>
                    <button wire:click="cancel" class="btn btn-dark nightowl-daylight">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
