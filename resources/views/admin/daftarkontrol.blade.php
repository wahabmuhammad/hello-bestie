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
                    Menu Notifikasi Jadwal Kontrol Pasien
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
                    <a href="#" class="btn btn-white d-sm-none btn-icon" data-bs-toggle="modal"
                        data-bs-target="#modal-report" aria-label="Buat User Baru">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-export"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3" />
                        </svg>
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
                    {{-- <a class="btn btn-cyan d-none d-sm-inline-block">
                            
                        </a> --}}
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
                    <a href="#" class="btn btn-cyan d-sm-none btn-icon" data-bs-toggle="modal"
                        data-bs-target="#modal-report" aria-label="Buat User Baru">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-import"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5m-9.5 -2h7m-3 -3l3 3l-3 3" />
                        </svg>
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
                    <table class="table table-vcenter table-mobile-md card-table">
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
                    <div class="pagination-links" id="pagination-links">
                        {{-- {{ $datas->withQueryString()->links() }} <!-- Menampilkan link pagination --> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal list --}}
    <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Membuat Peserta Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label strong">Nama</label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Nama Lengkap dan Gelar">
                        </div>
                        <div class="mb-3">
                            <label class="form-label strong">NIK</label>
                            <input type="password" class="form-control" name="text" id="password"
                                placeholder="Password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label strong">No Hp</label>
                            <input type="text" class="form-control" name="email" id="email"
                                placeholder="No HP">
                        </div>
                        <div class="mb-3">
                            <label class="form-label strong">Alamat</label>
                            <input type="text" class="form-control" name="nip" id="nip"
                                placeholder="Alamat">
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label strong">Universitas/Sekolah</label>
                                    <input type="text" class="form-control" name="nip" id="nip"
                                        placeholder="Universitas/Sekolah">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label strong">STR</label>
                                    <input type="text" class="form-control" name="nip" id="nip"
                                        placeholder="STR">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label strong">Rekomendasi</label>
                            <input type="text" class="form-control" name="nip" id="nip"
                                placeholder="Rekomendasi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label strong">Sertifikat</label>
                            <input type="text" class="form-control" name="nip" id="nip"
                                placeholder="Sertifikat">
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label strong">Pendidikan</label>
                                    <div class="input-group input-group-flat">
                                        <input name="jabatan" id="jabatan" type="text" class="form-control"
                                            autocomplete="off" placeholder="Pendidikan">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label strong">Formasi</label>
                                    <select class="form-select" name="role" id="role">
                                        {{-- @foreach ($datapeserta as $data)
                                            <option value="{{ $data->Formasi }}">{{ $data->Formasi }}</option>
                                        @endforeach --}}
                                        {{-- <option value="Perawat Bedah" selected>Perawat Bedah</option>
                                        <option value="Perawat ICU">Perawat ICU</option>
                                        <option value="Driver">Driver</option>
                                        <option value="Staff IPSRS">Staff IPSRS</option>
                                        <option value="Juru Masak">Juru Masak</option>
                                        <option value="Penatalaksana Laundry">Penatalaksana Laundry</option>
                                        <option value="Sanitasi">Sanitasi</option>
                                        <option value="SDI">SDI</option>
                                        <option value="Kasir">Kasir</option>
                                        <option value=""></option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                            Batal
                        </a>
                        <input class="btn btn-primary ms-auto" type="submit" name="create-user" id="create-user"
                            value="Buat Akun User">
                    </div>
                </div>
            </form>
        </div>
    </div>

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

                $.ajax({
                    url: 'kontrol?page=' + page,
                    type: 'get',
                    data: {
                        tglAwal: tglAwal,
                        tglAkhir: tglAkhir,
                        search: search
                    },
                    dataType: 'json',
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
                    }
                });
            }

            // Trigger saat klik tombol "Cari"
            // $('#searchButton').on('click', function() {
            //     const tglAwal = $('#tglAwal').val();
            //     const tglAkhir = $('#tglAkhir').val();
            //     const search = $('#searchInput').val();

            //     $.ajax({
            //         url: 'kontrol/search',
            //         type: 'get',
            //         data: {
            //             tglAwal: tglAwal,
            //             tglAkhir: tglAkhir,
            //             search: search
            //         },
            //         dataType: 'json',
            //         success: function(response) {
            //             let rows = '';
            //             $.each(response.datas, function(index, data) {
            //                 rows += `<tr class="patient-row" data-emrpasien="${data.noemr}">
        //                         <td>${index + 1}</td>
        //                         <td>${data.namapasien}</td>
        //                         <td>${data.nocm}</td>
        //                         <td>${data.tgllahir}</td>
        //                         <td>${data.nohp}</td>
        //                         <td>${data.namaruangan}</td>
        //                         <td>${data.namadokter}</td>
        //                         <td>${data.tglkontrol}</td>
        //                     </tr>`;
            //             });
            //             $('#table-body').html(rows);
            //             $('#pagination-links').html(response.pagination);
            //         },
            //         error: function() {
            //             alert('Terjadi kesalahan saat mengambil data.');
            //         }
            //     });
            // });

            let currentTglAwal = $('#tglAwal').val();
            let currentTglAkhir = $('#tglAkhir').val();
            let currentSearch = $('#searchInput').val();
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

            $('#kirim-notifikasi').on('click', function() {
                $.ajax({
                    url: 'kontrol/send-notification',
                    type: 'post',
                    data: {
                        tglAwal: $('#tglAwal').val(),
                        tglAkhir: $('#tglAkhir').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Pesan notifikasi telah dikirim ke semua pasien.",
                            icon: "success",
                            confirmButtonText: "Selesai"
                        });
                    },
                    error: function() {
                        alert('Gagal mengirim notifikasi.');
                    }
                });
            });

        });
    </script>
@endsection
