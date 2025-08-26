{{-- Modal  --}}
<div class="modal modal-blur fade" id="modal-formnotifikasi" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        {{-- <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="PUT" id="formEditLayanan">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label">Kelompok Produk</label>
                                <input type="text" id="modalkelompokproduk" name="kelompokproduk"
                                    class="form-control" data-suggest-modal="modalkelompokproduk">
                                <input type="hidden" id="modalidkelompokproduk" name="idkelompokproduk">
                                <input type="hidden" id="idproduk" name="idproduk">
                                <ul id="modalsuggestionsListKelompok" class="list-group"
                                    style="display: none; z-index: 1000;">
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk Layanan</label>
                                <input type="text" name="namaproduk" id="modal-editnamaproduk"
                                    class="form-control @error('namaPasien') is-invalid @enderror">
                                @error('namaPasien')
                                    <div class="invalid-feedback">Nama Pasien harus diisi</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label">Harga</label>
                                <input type="text" name="harga" id="modal-editharga"
                                    class="form-control @error('namaPasien') is-invalid @enderror">
                                @error('namaPasien')
                                    <div class="invalid-feedback">Nama Pasien harus diisi</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Jenis Produk</label>
                                <input type="text" id="modaljenisproduk" name="jenisproduk" class="form-control"
                                    data-suggest-modal="modaljenisproduk">
                                <input type="hidden" id="modalidjenisproduk" name="idjenisproduk">
                                <ul id="modalsuggestionsListJenis" class="list-group"
                                    style="display: none; z-index: 1000;"></ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" id="modal-editsatuan"
                                class="form-control @error('tanggalLahir') is-invalid @enderror">
                            @error('tanggalLahir')
                                <div class="invalid-feedback">Tanggal Lahir harus diisi</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Detail Jenis</label>
                                <input type="text" id="modaldetailjenisproduk" name="detailjenisproduk"
                                    class="form-control" data-suggest-modal="modaldetailjenisproduk">
                                <input type="hidden" id="modaliddetailjenisproduk" name="iddetailjenisproduk">
                                <ul id="modalsuggestionsListDetail" class="list-group"
                                    style="display: none; z-index: 1000;">
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Penjamin</label>
                                <input type="text" id="modalnamapenjamin" name="namapenjamin" class="form-control"
                                    data-suggest-modal="modalnamapenjamin">
                                <input type="hidden" id="modalidnamapenjamin" name="idnamapenjamin">
                                <ul id="modalsuggestionsListPenjamin" class="list-group"
                                    style="display: none; z-index: 1000;">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary ms-auto" data-bs-dismiss="modal"
                        id="pasienDaftar">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>

        </div> --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs nav-fill" id="editLayananTab" role="tablist" style="justify-content: center" >
    
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="batal-tab" data-bs-toggle="tab" data-bs-target="#batal" type="button"
                        role="tab">
                        Batal Praktik / Cuti
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jadwal-tab" data-bs-toggle="tab" data-bs-target="#jadwal"
                        type="button" role="tab">
                        Rubah Jadwal Praktik
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3">

                <!-- TAB BATAL KONTROL / CUTI -->
                <div class="tab-pane fade show active" id="batal" role="tabpanel">
                    <form action="" method="POST" id="formBatalKontrol">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Awal Rencana Kontrol</label>
                                <input type="date" name="tglkontrolawal" class="form-control" id="tglkontrolawal-modal" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Awal Rencana Kontrol</label>
                                <input type="date" name="tglkontrolakhir" class="form-control" id="tglkontrolakhir-modal" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dokter</label>
                                <input type="text" name="dokter" class="form-control" id="dokter-modal" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ruangan</label>
                                <input type="text" name="ruangan" class="form-control" id="ruangan-modal" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alasan Batal / Cuti</label>
                                <textarea name="alasan" class="form-control" rows="3" id="alasan"></textarea>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-danger ms-auto">Simpan Batal / Cuti</button>
                        </div>
                    </form>
                </div>

                <!-- TAB RUBAH JADWAL KONTROL -->
                <div class="tab-pane fade" id="jadwal" role="tabpanel">
                    <form action="" method="POST" id="formRubahJadwal">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Jadwal Lama</label>
                                <input type="text" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jadwal Baru</label>
                                <input type="date" name="jadwalbaru" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-warning ms-auto">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
