<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class kirimKunjunganPenunjang implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tglAwal, $tglAkhir;
    /**
     * Create a new job instance.
     */
    public function __construct($tglAwal, $tglAkhir)
    {
        $this->tglAwal = $tglAwal;
        $this->tglAkhir = $tglAkhir;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $tglAwal = $this->tglAwal;
        $tglAkhir = $this->tglAkhir;
        $datas = DB::table('pasiendaftar_t')
            ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('kelas_m as kls', 'antrianpasiendiperiksa_t.objectkelasfk', '=', 'kls.id')
            ->leftJoin('pasien_m', 'pasiendaftar_t.nocmfk', '=', 'pasien_m.id')
            ->leftJoin('ruangan_m', 'antrianpasiendiperiksa_t.objectruanganfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->leftJoin('kelompokpasien_m', 'pasiendaftar_t.objectkelompokpasienlastfk', '=', 'kelompokpasien_m.id')
            ->leftJoin('pegawai_m', 'pasiendaftar_t.objectpegawaifk', '=', 'pegawai_m.id')
            ->leftJoin('rekanan_m', 'pasiendaftar_t.objectrekananfk', '=', 'rekanan_m.id')
            ->where('pasiendaftar_t.statusenabled', true)
            ->whereIn('departemen_m.id', ['27', '3', '24']) // ambil data rawat jalan id departemen 18
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween('pasiendaftar_t.tglregistrasi', [
                    $tglAwal . ' 00:00:00',
                    $tglAkhir . ' 23:59:59'
                ]);
            })
            ->distinct()
            ->select(
                'pasiendaftar_t.noregistrasi',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'kelompokpasien_m.namaexternal as jenis_pembayaran',
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
            ->orderBy('pasiendaftar_t.noregistrasi', 'asc')
            ->get();
        $delay = 0;
        foreach ($datas as $data) {
            $noregistrasi = $data->noregistrasi;
            $idRuangan = $data->idruangan;
            $tanggal_pulang = $data->tglpulang;

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
                ->where('pd.noregistrasi', '=', $noregistrasi)
                ->where('apd.objectruanganfk', '=', $idRuangan)
                ->orderBy('pp.tglpelayanan', 'asc')
                ->orderBy('pp.rke', 'asc')
                ->get();

            $pelayananpetugas = DB::table('pasiendaftar_t as pd')
                ->join('antrianpasiendiperiksa_t as apd', 'apd.noregistrasifk', '=', 'pd.norec')
                ->join('pelayananpasienpetugas_t as ptu', 'ptu.nomasukfk', '=', 'apd.norec')
                ->leftjoin('pegawai_m as pg', 'pg.id', '=', 'ptu.objectpegawaifk')
                ->select('ptu.pelayananpasien', 'pg.namalengkap', 'pg.id as iddokter')
                ->where('ptu.objectjenispetugaspefk', 4)
                ->where('pd.noregistrasi', '=', $noregistrasi)
                ->get();
            if (count($pelayanan) > 0) {
                $details = array();
                // $details = array();
                $total_biaya = 0;
                $total_tagihan = 0;
                $total_pembayaran = 0;
                foreach ($pelayanan as $item) {
                    if (
                        empty($item->prid) ||
                        empty($item->namaproduk) ||
                        empty($item->jumlah) ||
                        $item->jumlah <= 0
                    ) {
                        continue;
                    }
                    $NamaDokter = '-';
                    $kodeDokter = '';
                    foreach ($pelayananpetugas as $hahaha) {
                        if ($hahaha->pelayananpasien == $item->norec) {
                            $NamaDokter = $hahaha->namalengkap;
                            $kodeDokter = $hahaha->iddokter;
                        }
                    }

                    // $harga = (float)$item->hargajual;
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
                        'kode_penerima' => (string)$kodeDokter,
                        'nama_penerima' => $NamaDokter,
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
                'nomor_transaksi' => $noregistrasi . '~' . $data->kodeeksternal,
                'nomor_rekam_medis' => $data->nocm,
                'nama_pasien' => $data->namapasien,
                'jenis_pembayaran' => $data->jenis_pembayaran,
                'nama_penjamin' => $data->namarekanan,
                'tanggal_pelayanan' => $data->tglregistrasi,
                'tanggal_pulang' => $data->tglpulang,
                'kode_layanan' => $data->kodeeksternal,
                'nama_layanan' => $data->namaruangan,
                'kode_dokter' => (string)$data->iddokter,
                'nama_dokter' => $data->namadokter,
                'total_biaya'       => (float) $total_biaya,
                'total_pembayaran' => (float) $total_pembayaran,
                'total_tagihan'    => (float) $total_tagihan,
                'items' => $details
            ];
            dispatch(new kirimToBod($payload))
                ->delay(now()->addSeconds($delay));

            $delay += 2;

            Log::info('Payload kirimKunjunganRawatInap', $payload);
        }
    }
}
