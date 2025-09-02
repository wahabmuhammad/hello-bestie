@extends('layout.masterLayout')

@section('content')
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    Administrator
                </div>
                <h2 class="page-title">
                    Menu Notifikasi Batal dan Perubahan Jadwal Kontrol Pasien
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-white d-none d-sm-inline-block" data-bs-toggle="modal"
                        data-bs-target="#modal-report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-export"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3" />
                        </svg>
                        Export Excell
                    </a>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button class="btn btn-cyan d-none d-sm-inline-block" id="kirim-notifikasi">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-import"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5m-9.5 -2h7m-3 -3l3 3l-3 3" />
                        </svg>
                        Kirim Semua Notifikasi
                    </button>
                    <a href="#" class="btn btn-cyan d-none d-sm-inline-block" data-bs-toggle="modal"
                        data-bs-target="#modal-report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-import"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5m-9.5 -2h7m-3 -3l3 3l-3 3" />
                        </svg>
                        Kirim Single Notifikasi
                    </a>

                </div>
            </div>
            {{-- <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                        data-bs-target="#modal-report">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Tambah Peserta Baru
                    </a>
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                        data-bs-target="#modal-report" aria-label="Buat User Baru">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </a>
                </div>
            </div> --}}
        </div>

        <div class="col-12" style="margin-top: 30px">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-tittle">Daftar Pasien Kontrol</h3>
                </div>
                <div class="card-body border-bottom py-2">
                    <div class="ms-md-2 text-secondary">
                        <a>Tanggal Awal</a>
                        <input type="date" id="tglAwal" class="form-control" name="tglregistrasi">
                    </div>
                    <div class="ms-md-2 text-secondary">
                        <a>Tanggal Akhir</a>
                        <input type="date" id="tglAkhir" class="form-control" name="tglregistrasi">
                    </div>
                    <div class="ms-md-2 text-secondary">
                        <a>Ruangan</a>
                        <input type="text" id="ruangan" class="form-control" name="ruangan" placeholder="Cari Ruangan"
                            autocomplete="off">
                        <input type="hidden" id="idruangan" class="form-control" name="idruangan"
                            placeholder="Cari Ruangan" autocomplete="off">
                        <ul id="ruanganList" class="list-group"
                            style="display: none; z-index: 1000; max-height:200px; overflow-y:auto; width:100%;"></ul>
                    </div>
                    <div class="ms-md-2 text-secondary">
                        <a>Dokter</a>
                        <input type="text" id="dokter" class="form-control" name="dokter" placeholder="Cari Dokter"
                            autocomplete="off">
                        <input type="hidden" id="iddokter" class="form-control" name="iddokter" placeholder="Cari Dokter"
                            autocomplete="off">
                        <ul id="dokterList" class="list-group"
                            style="display: none; z-index: 1000; max-height:200px; overflow-y:auto; width:100%;"></ul>
                    </div>
                    <div class="ms-md-2 text-secondary">
                        <a>Cari</a>
                        <div class="input-group">
                            <input type="search" id="searchInput" class="form-control" placeholder="Search…"
                                name="search" aria-label="Search in website">
                            <button id="searchButton" class="btn btn-primary" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No RM</th>
                                <th>Tanggal Lahir</th>
                                <th>No HP</th>
                                <th>Poliklinik</th>
                                <th>Dokter</th>
                                <th>Tanggal Kontrol</th>
                            </tr>
                        </thead>
                        <tbody class="table-body" id="table-body">

                        </tbody>
                        <!-- Pagination Links -->
                    </table>

                </div>
                <div class="card-footer">
                    {{-- Loader --}}
                    <div id="loader" class="container container-slim py-4"
                        style="display: none; text-align:center; padding:20px;">
                        <div class="text-center">
                            <div class="text-secondary mb-3">Loading Data</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-indeterminate"></div>
                            </div>
                        </div>
                    </div>
                    {{-- End Loader --}}
                    <div class="pagination-links" id="pagination-links">
                        {{-- {{ $datas->withQueryString()->links() }} <!-- Menampilkan link pagination --> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layout.modal')


    <script type="text/javascript">
        $(document).ready(function() {
            // Set default tanggal hari ini jika belum diisi
            let today = new Date().toISOString().split('T')[0];
            $('#tglAwal').val($('#tglAwal').val() || today);
            $('#tglAkhir').val($('#tglAkhir').val() || today);

            // Fungsi untuk load data dari server
            function loadData(page = 1) {
                const tglAwal = $('#tglAwal').val();
                const tglAkhir = $('#tglAkhir').val();
                const search = $('#searchInput').val();
                const idRuangan = $('#idruangan').val();
                const iddokter = $('#iddokter').val();

                $.ajax({
                    url: 'batal-kontrol?page=' + page,
                    type: 'get',
                    data: {
                        tglAwal: tglAwal,
                        tglAkhir: tglAkhir,
                        search: search,
                        idruangan: idRuangan,
                        iddokter: iddokter
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#loader').show(); // tampilkan loader
                        $('#table-body').html(''); // kosongkan isi tabel biar tidak bingung
                    },
                    success: function(response) {
                        let rows = '';
                        const startIndex = (page - 1) * 10;

                        $.each(response.datas, function(index, data) {
                            const rowNumber = startIndex + index + 1;

                            rows += `<tr class="patient-row" data-emrpasien="${data.noemr}">
                                    <td>${rowNumber}</td>
                                    <td>${data.namapasien}</td>
                                    <td>${data.nocm}</td>
                                    <td>${data.tgllahir}</td>
                                    <td>${data.nohp}</td>
                                    <td>${data.namaruangan}</td>
                                    <td>${data.namadokter}</td>
                                    <td>${data.tglkontrol}</td>
                                </tr>`;
                        });

                        $('#table-body').html(rows);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengambil data.');
                    },
                    complete: function() {
                        $('#loader').hide(); // sembunyikan loader setelah selesai
                    }

                });
            }

            let currentTglAwal = $('#tglAwal').val();
            let currentTglAkhir = $('#tglAkhir').val();
            let currentSearch = $('#searchInput').val();
            let idRuangan = $('#idruangan').val();
            let iddokter = $('#iddokter').val();

            // // Trigger otomatis saat tanggal diubah
            $('#searchButton').on('click', function() {
                // Simpan parameter ke variabel global
                currentTglAwal = $('#tglAwal').val();
                currentTglAkhir = $('#tglAkhir').val();
                currentSearch = $('#searchInput').val();

                loadData(1); // Panggil ulang data berdasarkan pencarian
            });

            // Tangani klik link pagination
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                loadData(page);
            });

            // Load data awal
            loadData();

            // Autocomplete event
            $('#ruangan').on('keyup', function() {
                let input = $(this);
                let query = input.val();

                if (query.length < 1) {
                    $('#idruangan').val('');

                    $('#ruanganList').hide();
                    return;
                }

                $.ajax({
                    url: '/kontrol/get-ruangan',
                    method: 'GET',
                    data: {
                        query: query
                    },
                    success: function(response) {
                        console.log(response); // cek isi responsenya
                        let list = $('#ruanganList');
                        list.empty();

                        if (response.datas && response.datas.length > 0) {
                            response.datas.forEach(function(item) {
                                list.append(
                                    '<li class="list-group-item ruangan-item" data-id="' +
                                    item.id + '">' + item.namaruangan + '</li>'
                                );
                            });
                            list.show();
                        } else {
                            list.hide();
                        }
                    }
                });
            });

            // Klik suggestion -> isi input
            $(document).on('click', '.ruangan-item', function() {
                let nama = $(this).text();
                let id = $(this).data('id');

                $('#ruangan').val(nama); // tampilkan nama di input
                $('#idruangan').val(id); // simpan id di attribute data-id
                $('#ruanganList').hide();
            });

            // Klik di luar -> hide list
            $(document).click(function(e) {
                if (!$(e.target).closest('#ruangan, #ruanganList').length) {
                    $('#ruanganList').hide();
                }
            });

            // Autocomplete untuk dokter
            $('#dokter').on('keyup', function() {
                let input = $(this);
                let query = input.val();

                if (query.length < 1) {
                    $('#iddokter').val('');

                    $('#dokterList').hide();
                    return;
                }

                $.ajax({
                    url: '/kontrol/get-dokter',
                    method: 'GET',
                    data: {
                        query: query
                    },
                    success: function(response) {
                        console.log(response); // cek isi responsenya
                        let list = $('#dokterList');
                        list.empty();

                        if (response.datas && response.datas.length > 0) {
                            response.datas.forEach(function(item) {
                                list.append(
                                    '<li class="list-group-item dokter-item" data-id="' +
                                    item.id + '">' + item.namadokter + '</li>'
                                );
                            });
                            list.show();
                        } else {
                            list.hide();
                        }
                    }
                });
            });

            // Klik suggestion -> isi input
            $(document).on('click', '.dokter-item', function() {
                let nama = $(this).text();
                let id = $(this).data('id');

                $('#dokter').val(nama); // tampilkan nama di input
                $('#iddokter').val(id); // simpan id di attribute data-id
                $('#dokterList').hide();
            });

            // Klik di luar -> hide list
            $(document).click(function(e) {
                if (!$(e.target).closest('#dokter, #dokterList').length) {
                    $('#dokterList').hide();
                }
            });

            //memunculkan modal notifikasi
            let dokter = $('#dokter').val();

            $('#kirim-notifikasi').on('click', function() {
                let dokter = $('#dokter').val(); // ambil nama dokter terbaru
                let iddokter = $('#iddokter').val(); // kalau butuh ID juga
                let ruangan = $('#ruangan').val(); // ambil nama ruangan terbaru
                let idruangan = $('#idruangan').val(); // kalau butuh ID juga

                $('#modal-formnotifikasi').modal('show');
                //Tab Batal Kontrol
                $('#tglkontrolawal-modal').val(currentTglAwal);
                $('#tglkontrolakhir-modal').val(currentTglAkhir);
                $('#dokter-modal').val(dokter);
                $('#iddokter-modal').val(iddokter);
                $('#idruangan-modal').val(idruangan);
                $('#ruangan-modal').val(ruangan);
                // Tab Perubahan Jadwal
                $('#tglkontrolawal-jadwal').val(currentTglAwal);
                $('#tglkontrolakhir-jadwal').val(currentTglAkhir);
                $('#dokter-jadwal').val(dokter);
                $('#iddokter-jadwal').val(iddokter);
                $('#ruangan-jadwal').val(ruangan);
                $('#idruangan-jadwal').val(idruangan);
                // console.log(dokter);
            });

            //handle form batal kontrol submit
            // Submit form notifikasi kontrol
            $('#formBatalKontrol').on('submit', function(e) {
                e.preventDefault(); // cegah form submit default dulu
                let alasan = $('#alasan').val().trim();
                console.log(alasan);
                Swal.fire({
                    title: 'Kirim Notifikasi',
                    text: "Apakah Anda yakin ingin mengirim notifikasi ke semua pasien sesuai kriteria?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/kontrol/send-notification/batal-kontrol',
                            type: 'post',
                            data: {
                                tglAwal: $('#tglAwal').val(),
                                tglAkhir: $('#tglAkhir').val(),
                                iddokter: $('#iddokter').val(),
                                idruangan: $('#idruangan').val(),
                                alasan: $('#alasan').val(),
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Pesan notifikasi telah dikirim ke semua pasien.",
                                    icon: "success",
                                    confirmButtonText: "Selesai"
                                });
                                $('#modal-formnotifikasikontrol').modal('hide');
                            },
                            error: function() {
                                Swal.fire({
                                    title: "Gagal",
                                    text: "Gagal mengirimkan notifikasi ke semua pasien.",
                                    icon: "error",
                                    confirmButtonText: "OK!"
                                });
                            }
                        });
                    }
                });
            });

            $('#formRubahJadwal').on('submit', function(e) {
                e.preventDefault(); // cegah form submit default dulu
                // let alasan = $('#alasan').val().trim();
                console.log(alasan);
                Swal.fire({
                    title: 'Kirim Notifikasi',
                    text: "Apakah Anda yakin ingin mengirim notifikasi ke semua pasien sesuai kriteria?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/kontrol/send-notification/rubah-jadwal',
                            type: 'post',
                            data: {
                                tglAwal: $('#tglkontrolawal-jadwal').val(),
                                tglAkhir: $('#tglkontrolakhir-jadwal').val(),
                                iddokter: $('#iddokter-jadwal').val(),
                                idruangan: $('#idruangan-jadwal').val(),
                                jadwalPraktik: $('#jadwalPraktik').val(),
                                jamPraktik: $('#jamPraktik').val(),
                                akhirjamPraktik: $('#akhirjamPraktik').val(),
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Pesan notifikasi telah dikirim ke semua pasien.",
                                    icon: "success",
                                    confirmButtonText: "Selesai"
                                });
                                $('#modal-formnotifikasikontrol').modal('hide');
                            },
                            error: function() {
                                Swal.fire({
                                    title: "Gagal",
                                    text: "Gagal mengirimkan notifikasi ke semua pasien.",
                                    icon: "error",
                                    confirmButtonText: "OK!"
                                });
                            }
                        });
                    }
                });
            });

            // $('#formBatalKontrol').on('submit', function(e) {
            //     e.preventDefault(); // cegah submit biasa

            //     let form = $(this);
            //     let url = form.attr('action');
            //     let data = form.serialize();

            //     $.ajax({
            //         url: `/kontrol/send-notification/batal-kontrol`,
            //         type: "POST",
            //         data: data,
            //         beforeSend: function() {
            //             // Bisa tambahkan loader
            //             $('.btn-danger').prop('disabled', true).text('Menyimpan...');
            //         },
            //         success: function(response) {
            //             // contoh response sukses
            //             alert(response.message || 'Data berhasil disimpan!');
            //             $('#formBatalKontrol')[0].reset();
            //             $('#modalBatalKontrol').modal('hide'); // jika ada modal
            //             // reload table / data
            //         },
            //         error: function(xhr) {
            //             let res = xhr.responseJSON;
            //             if (res && res.errors) {
            //                 let msg = '';
            //                 $.each(res.errors, function(key, value) {
            //                     msg += value[0] + "\n";
            //                 });
            //                 alert(msg);
            //             } else {
            //                 alert("Terjadi kesalahan, silakan coba lagi.");
            //             }
            //         },
            //         complete: function() {
            //             $('.btn-danger').prop('disabled', false).text('Simpan Batal / Cuti');
            //         }
            //     });
            // });
        });
    </script>
@endsection
