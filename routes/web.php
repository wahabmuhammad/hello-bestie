<?php

use App\Http\Controllers\indexKunjunganController;
use App\Http\Controllers\kontrolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [kontrolController::class, 'indexhome'])->name('homeKontrol.index');
Route::get('/kontrol', [kontrolController::class, 'index'])->name('pasienKontrol.index');
Route::get('/kontrol/search', [kontrolController::class, 'searchData'])->name('searchData');
Route::get('/get-data-pasien-kontrol', [kontrolController::class,'getDataPasienKontrol'])->name('getDataPasienKontrol');
Route::post('/kontrol/send-notification', [kontrolController::class, 'kirimNotifikasi'])->name('kirimNotifikasi');
Route::get('/kontrol/batal-kontrol', [kontrolController::class, 'batalKontrol'])->name('batalKontrol');
Route::get('/kontrol/get-ruangan', [kontrolController::class, 'getRuangan'])->name('getRuangan');
Route::get('/kontrol/get-dokter', [kontrolController::class, 'getDokter'])->name('getDokter');
Route::get('/daftar-kunjungan-kontrol', [indexKunjunganController::class, 'daftarKunjunganKontrol'])->name('daftarKunjunganKontrol');
Route::post('/daftar-kunjungan/send-data', [indexKunjunganController::class, 'sendDataKunjungan'])->name('sendDataKunjungan');
Route::get('/daftar-kunjungan-ranap', [indexKunjunganController::class, 'indexKunjunganRanap'])->name('indexKunjunganRanap');
Route::post('/daftar-kunjungan-ranap/send-data', [indexKunjunganController::class, 'sendDataKunjunganRanap'])->name('sendDataKunjunganRanap');
Route::get('/daftar-kunjungan-penunjang', [indexKunjunganController::class, 'indexKunjunganPenunjang'])->name('indexKunjunganPenunjang');
Route::post('/daftar-kunjungan-penunjang/send-data', [indexKunjunganController::class, 'sendDataKunjunganPenunjang'])->name('sendDataKunjunganPenunjang');
Route::get('/daftar-kunjungan-ranap/send-data/{noreg}/{idRuangan}', [indexKunjunganController::class, 'sendDataKunjunganRanapByNoreg'])->name('sendDataKunjunganRanapByNoreg');

Route::post('/kontrol/send-notification/batal-kontrol', [kontrolController::class, 'notifikasiBatalPraktik'])->name('notifikasiBatalPraktik');
Route::post('/kontrol/send-notification/rubah-jadwal', [kontrolController::class, 'rubahJadwal'])->name('rubahJadwal');
