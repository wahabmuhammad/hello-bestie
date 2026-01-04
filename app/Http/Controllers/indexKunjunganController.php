<?php

namespace App\Http\Controllers;

use App\Jobs\kirimKunjunganRawatInap;
use App\Jobs\kirimKunjunganRawatJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class indexKunjunganController extends Controller
{
    public function daftarKunjunganKontrol(Request $request)
    {
        $search = $request->input('search');
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');
        $idRuangan = $request->input('idruangan');
        $idDokter = $request->input('iddokter');

        $pasiendaftar = DB::table('pasiendaftar_t')
            ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('pasien_m', 'pasiendaftar_t.nocmfk', '=', 'pasien_m.id')
            ->leftJoin('ruangan_m', 'antrianpasiendiperiksa_t.objectruanganfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->leftJoin('kelompokpasien_m', 'pasiendaftar_t.objectkelompokpasienlastfk', '=', 'kelompokpasien_m.id')
            ->leftJoin('pegawai_m', 'pasiendaftar_t.objectpegawaifk', '=', 'pegawai_m.id')
            ->where('pasiendaftar_t.statusenabled', true)
            // ->where('pasiendaftar_t.objectkelompokpasienlastfk', '!=', 2)
            // ->whereIn('departemen_m.id', ['18','3','24']) // ambil data rawat jalan id departemen 18
            ->where('departemen_m.id', '=', '18') // ambil data rawat jalan id departemen 18
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween('pasiendaftar_t.tglregistrasi', [
                    $tglAwal . ' 00:00:00',
                    $tglAkhir . ' 23:59:59'
                ]);
            })
            ->distinct()
            ->select(
                'pasiendaftar_t.norec as norec_pd',
                'pasiendaftar_t.noregistrasi',
                'antrianpasiendiperiksa_t.norec as norec_apd',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasiendaftar_t.tglregistrasi',
                'pegawai_m.namalengkap as namadokter',
                'pasiendaftar_t.tglpulang',
                'pegawai_m.id as iddokter',
                // 'ruangan_m.id as idruangan',
                'ruangan_m.namaruangan',
                'kelompokpasien_m.kelompokpasien'
            )
            ->orderBy('pasiendaftar_t.noregistrasi', 'asc')
            ->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'datas' => $pasiendaftar->items(),
                'pagination' => (string) $pasiendaftar->links()
            ]);
        }

        return view('admin.indexKunjungan');
    }

    public function sendDataKunjungan(Request $request)
    {
        // dd($request->all());
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        dispatch(new kirimKunjunganRawatJalan($tglAwal, $tglAkhir));

        return response()->json(['status' => 'success', 'message' => 'Data kunjungan berhasil diproses.']);
    }

    public function indexKunjunganRanap(Request $request)
    {
        $search = $request->input('search');
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');
        $idRuangan = $request->input('idruangan');
        $idDokter = $request->input('iddokter');

        $pasiendaftar = DB::table('pasiendaftar_t')
            ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('pasien_m', 'pasiendaftar_t.nocmfk', '=', 'pasien_m.id')
            ->leftJoin('ruangan_m', 'antrianpasiendiperiksa_t.objectruanganfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->leftJoin('kelompokpasien_m', 'pasiendaftar_t.objectkelompokpasienlastfk', '=', 'kelompokpasien_m.id')
            ->leftJoin('pegawai_m', 'pasiendaftar_t.objectpegawaifk', '=', 'pegawai_m.id')
            ->where('pasiendaftar_t.statusenabled', true)
            ->where('departemen_m.id', '=', '16') // ambil data rawat inap id departemen 16
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween('pasiendaftar_t.tglregistrasi', [
                    $tglAwal . ' 00:00:00',
                    $tglAkhir . ' 23:59:59'
                ]);
            })
            ->distinct()
            ->select(
                'pasiendaftar_t.norec as norec_pd',
                'pasiendaftar_t.noregistrasi',
                'antrianpasiendiperiksa_t.norec as norec_apd',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasiendaftar_t.tglregistrasi',
                'pegawai_m.namalengkap as namadokter',
                'pasiendaftar_t.tglpulang',
                'pegawai_m.id as iddokter',
                // 'ruangan_m.id as idruangan',
                'ruangan_m.namaruangan',
                'kelompokpasien_m.kelompokpasien'
            )
            ->orderBy('pasiendaftar_t.noregistrasi', 'asc')
            ->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'datas' => $pasiendaftar->items(),
                'pagination' => (string) $pasiendaftar->links()
            ]);
        }
        return view('admin.indexKunjunganRanap');
    }

    public function sendDataKunjunganRanap(Request $request)
    {
        // dd($request->all());
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        dispatch(new kirimKunjunganRawatInap($tglAwal, $tglAkhir));

        return response()->json(['status' => 'success', 'message' => 'Data kunjungan rawat inap berhasil diproses.']);
    }

    public function indexKunjunganPenunjang(Request $request)
    {
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        $pasiendaftar = DB::table('pasiendaftar_t')
            ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('pasien_m', 'pasiendaftar_t.nocmfk', '=', 'pasien_m.id')
            ->leftJoin('ruangan_m', 'antrianpasiendiperiksa_t.objectruanganfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->leftJoin('kelompokpasien_m', 'pasiendaftar_t.objectkelompokpasienlastfk', '=', 'kelompokpasien_m.id')
            ->leftJoin('pegawai_m', 'pasiendaftar_t.objectpegawaifk', '=', 'pegawai_m.id')
            ->where('pasiendaftar_t.statusenabled', true)
            // ->where('pasiendaftar_t.objectkelompokpasienlastfk', '!=', 2)
            ->whereIn('departemen_m.id', ['27','3','24']) // ambil data rawat jalan id departemen 18
            // ->where('departemen_m.id', '=', '18') // ambil data rawat jalan id departemen 18
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween('pasiendaftar_t.tglregistrasi', [
                    $tglAwal . ' 00:00:00',
                    $tglAkhir . ' 23:59:59'
                ]);
            })
            ->distinct()
            ->select(
                'pasiendaftar_t.norec as norec_pd',
                'pasiendaftar_t.noregistrasi',
                'antrianpasiendiperiksa_t.norec as norec_apd',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasiendaftar_t.tglregistrasi',
                'pegawai_m.namalengkap as namadokter',
                'pasiendaftar_t.tglpulang',
                'pegawai_m.id as iddokter',
                // 'ruangan_m.id as idruangan',
                'ruangan_m.namaruangan',
                'kelompokpasien_m.kelompokpasien'
            )
            ->orderBy('pasiendaftar_t.noregistrasi', 'asc')
            ->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'datas' => $pasiendaftar->items(),
                'pagination' => (string) $pasiendaftar->links()
            ]);
        }

        return view('admin.indexKunjunganPenunjang');
    }

    public function sendDataKunjunganPenunjang(Request $request)
    {
        //
    }
}
