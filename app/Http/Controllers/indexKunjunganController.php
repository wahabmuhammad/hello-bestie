<?php

namespace App\Http\Controllers;

use App\Jobs\kirimKunjunganPenunjang;
use App\Jobs\kirimKunjunganRawatInap;
use App\Jobs\kirimKunjunganRawatJalan;
use App\services\bodService;
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
                'kelompokpasien_m.kelompokpasien',
                'antrianpasiendiperiksa_t.objectkelasfk as idkelas'
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
            ->whereIn('departemen_m.id', ['27', '3', '24']) // ambil data rawat jalan id departemen 18
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
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        dispatch(new kirimKunjunganPenunjang($tglAwal, $tglAkhir));

        return response()->json(['status' => 'success', 'message' => 'Data kunjungan rawat inap berhasil diproses.']);
    }

    public function sendDataKunjunganRanapByNoreg(Request $request, $noreg, $idRuangan)
    {
        // dd($noreg, $idRuangan);
        $data = DB::table('pasiendaftar_t')
            ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('kelas_m as kls', 'antrianpasiendiperiksa_t.objectkelasfk', '=', 'kls.id')
            ->leftJoin('pasien_m', 'pasiendaftar_t.nocmfk', '=', 'pasien_m.id')
            ->leftJoin('ruangan_m', 'antrianpasiendiperiksa_t.objectruanganfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->leftJoin('kelompokpasien_m', 'pasiendaftar_t.objectkelompokpasienlastfk', '=', 'kelompokpasien_m.id')
            ->leftJoin('pegawai_m', 'pasiendaftar_t.objectpegawaifk', '=', 'pegawai_m.id')
            ->leftJoin('rekanan_m', 'pasiendaftar_t.objectrekananfk', '=', 'rekanan_m.id')
            // ambil data rawat jalan id departemen 18
            ->where('pasiendaftar_t.noregistrasi', '=', $noreg)
            ->select(
                'pasiendaftar_t.noregistrasi',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'kelompokpasien_m.kelompokpasien',
                'rekanan_m.namarekanan',
                'pasiendaftar_t.tglregistrasi',
                'pasiendaftar_t.tglpulang',
                'ruangan_m.kodeeksternal',
                'ruangan_m.namaruangan',
                'pegawai_m.id as iddokter',
                'pegawai_m.namalengkap as namadokter',
                'pasiendaftar_t.norec as norec_pd',
                'antrianpasiendiperiksa_t.norec as norec_apd',
                'ruangan_m.id as idruangan',
                'antrianpasiendiperiksa_t.objectkelasfk as idkelas',
                'kls.namakelas',
                // 'pegawai_m.namalengkap as namadokter',
                // 'ruangan_m.namaruangan',
                // 'kelompokpasien_m.kelompokpasien'
            )
            ->first();
        $pelayanan = DB::table('pasiendaftar_t as pd')
            ->leftjoin('antrianpasiendiperiksa_t as apd', 'apd.noregistrasifk', '=', 'pd.norec')
            ->leftjoin('pelayananpasien_t as pp', 'pp.noregistrasifk', '=', 'apd.norec')
            ->leftjoin('pegawai_m as pgdokter', 'pgdokter.id', '=', 'pp.pelayananpegawaifk')
            ->leftjoin('produk_m as pr', 'pr.id', '=', 'pp.produkfk')
            ->leftjoin('detailjenisproduk_m as djp', 'djp.id', '=', 'pr.objectdetailjenisprodukfk')
            ->leftjoin('jenisproduk_m as jp', 'jp.id', '=', 'djp.objectjenisprodukfk')
            ->leftjoin('kelompokprodukbpjs_m as kpBpjs', 'kpBpjs.id', '=', 'pr.objectkelompokprodukbpjsfk')
            ->leftjoin('kelas_m as kls', 'kls.id', '=', 'apd.objectkelasfk')
            ->leftjoin('strukresep_t as sr', 'sr.norec', '=', 'pp.strukresepfk')
            ->leftjoin('ruangan_m as rusr', 'rusr.id', '=', 'sr.ruanganfk')
            ->leftjoin('ruangan_m as ru', 'ru.id', '=', 'apd.objectruanganfk')
            ->leftjoin('pegawai_m as pgsr', 'pgsr.id', '=', 'sr.penulisresepfk')
            ->leftjoin('strukpelayanan_t as sp', 'sp.norec', '=', 'pp.strukfk')
            ->leftjoin('strukpelayananpenjamin_t as sppj', 'sp.norec', '=', 'sppj.nostrukfk')
            ->leftjoin('strukbuktipenerimaan_t as sbm', 'sp.nosbmlastfk', '=', 'sbm.norec')
            ->leftjoin('pegawai_m as pgsbm', 'pgsbm.id', '=', 'sbm.objectpegawaipenerimafk')
            ->select(
                'pp.norec',
                'pp.tglpelayanan',
                // 'pp.rke',
                'ru.kodeeksternal',
                'pr.id as prid',
                'pr.namaproduk',
                'pp.jumlah',
                'pp.produkfk',
                'pp.hargajual',
                'pp.hargadiscount',
                'sp.nostruk',
                'sp.tglstruk',
                'sbm.nosbm',
                'sp.norec as norec_sp',
                'pp.jasa',
                'ru.id as ruid',
                'ru.namaruangan',
                'sr.noresep',
                'rusr.namaruangan as ruanganfarmasi',
                'pgsr.namalengkap as penulisresep',
                'pgdokter.namalengkap as namadokter_pp',
                'kpBpjs.kelompokprodukbpjs as kelompokprodukbpjs',
                'kls.id as klsid',
                'kls.namakelas',
                'pp.jasa',
                'sp.totalharusdibayar',
                'sp.totalprekanan',
                'sppj.totalppenjamin',
                'sp.totalbiayatambahan',
                'pgsbm.namalengkap as namalengkapsbm',
                'pp.aturanpakai',
                'pp.iscito',
                'pp.isparamedis'
            )
            // ->whereNull('pp.aturanpakai')
            ->where('pd.noregistrasi', '=', $noreg)
            // ->where('apd.objectruanganfk', '=', $idRuangan)
            ->orderBy('pp.tglpelayanan', 'asc')
            ->orderBy('pp.rke', 'asc')
            ->get();
        $pelayananpetugas = DB::table('pasiendaftar_t as pd')
            ->join('antrianpasiendiperiksa_t as apd', 'apd.noregistrasifk', '=', 'pd.norec')
            ->join('pelayananpasienpetugas_t as ptu', 'ptu.nomasukfk', '=', 'apd.norec')
            ->leftjoin('pegawai_m as pg', 'pg.id', '=', 'ptu.objectpegawaifk')
            ->select('ptu.pelayananpasien', 'pg.namalengkap', 'pg.id as iddokter')
            ->where('ptu.objectjenispetugaspefk', 4)
            ->where('pd.noregistrasi', '=', $noreg)
            ->get();
        if (count($pelayanan) > 0) {
            $details = array();
            $total_biaya = 0;
            $total_tagihan = 0;
            $total_pembayaran = 0;
            foreach ($pelayanan as $item) {
                $NamaDokter = '-';
                $kodeDokter = '';
                foreach ($pelayananpetugas as $hahaha) {
                    if ($hahaha->pelayananpasien == $item->norec) {
                        $NamaDokter = $hahaha->namalengkap;
                        $kodeDokter = $hahaha->iddokter;
                    }
                }


                $tanggal_pulang = $data->tglpulang ? $data->tglpulang : date('Y-m-d H:i:s');
                $harga = (float) $item->hargajual;
                $qty   = (float) $item->jumlah;

                $subtotal = $harga * $qty;

                $total_biaya += $subtotal;
                $total_tagihan += $subtotal;
                $total_pembayaran += $subtotal;
                // $diskon = (float)$item->hargadiscount;
                $detail = [
                    'kode_tindakan' => (string)$item->prid,
                    'nama_tindakan' => $item->namaproduk,
                    'harga_satuan' => $harga,
                    'qty_transaksi' => $item->jumlah,
                    'total_transaksi' => $item->hargajual * $item->jumlah,
                    'is_jasa' => $item->jasa > 0 ? true : false,
                    'kode_layanan' => $item->kodeeksternal,
                    'nama_layanan' => $item->namaruangan,
                    'kode_penerima' => (string)$kodeDokter,
                    'nama_penerima' => $NamaDokter,
                    'tanggal_pulang' => $tanggal_pulang,
                    // 'norec_pp' => $item->norec,
                    // 'tglpelayanan' => $item->tglpelayanan,
                    // 'rke' => $item->rke,
                    // 'prid' => $item->prid,
                    // 'nostruk' => $item->nostruk,
                    // 'tglstruk' => $item->tglstruk,
                    // 'nosbm' => $item->nosbm,
                    // 'norec_sp' => $item->norec_sp,
                    // 'jasa' => $item->jasa,
                    // 'ruid' => $item->ruid,
                    // 'namaruangan' => $item->namaruangan,
                    // 'noresep' => $item->noresep,
                    // 'ruanganfarmasi' => $item->ruanganfarmasi,
                    // 'penulisresep' => $item->penulisresep,
                    // 'namadokter_pp' => $item->namadokter_pp,
                    // 'kelompokprodukbpjs' => $item->kelompokprodukbpjs,
                    // 'klsid' => $item->klsid,
                    // 'namakelas' => $item->namakelas,
                    // 'totalharusdibayar' => $item->totalharusdibayar,
                    // 'totalprekanan' => $item->totalprekanan,
                    // 'totalppenjamin' => $item->totalppenjamin,
                    // 'totalbiayatambahan' => $item->totalbiayatambahan,
                    // 'namalengkapsbm' => $item->namalengkapsbm,
                ];
                $details[] = $detail;
            }
        }
        $payload = array();
        $payload = [
            'jenis_pembayaran' => $data->kelompokpasien,
            'kode_dokter' => (string)$data->iddokter,
            'kode_kelas' => (string)$data->idkelas,
            'kode_ruangan' => (string)$data->idruangan,
            'nama_dokter' => $data->namadokter,
            'nama_kelas' => $data->namakelas,
            'nama_pasien' => $data->namapasien,
            'nama_penjamin' => $data->namarekanan,
            'nama_ruangan' => $data->namaruangan,
            'nomor_rekam_medis' => $data->nocm,
            'nomor_transaksi' => $noreg,
            'tanggal_masuk' => $data->tglregistrasi,
            'tanggal_pulang' => $data->tglpulang,
            // 'kode_layanan' => $data->kodeeksternal,
            // 'nama_layanan' => $data->namaruangan,
            'total_biaya' => $total_biaya,
            'total_pembayaran' => $total_pembayaran,
            'total_tagihan' => $total_tagihan,
            'items' => $details
        ];
        // dd($payload);
        bodService::updateDataPasien($noreg,$payload);
        // dispatch(new kirimKunjunganRawatInap(null, null, $noreg));

        return response()->json(['status' => 'success', 'message' => 'Data kunjungan rawat inap untuk noreg ' . $noreg . ' berhasil diproses.']);
    }
}
