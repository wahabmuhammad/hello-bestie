<?php

namespace App\Jobs;

use App\services\bodService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class kirimKunjunganRawatJalan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $tglAkhir, $tglAwal;
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
            // ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('pasien_m', 'pasiendaftar_t.nocmfk', '=', 'pasien_m.id')
            ->leftJoin('ruangan_m', 'pasiendaftar_t.objectruanganlastfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->leftJoin('kelompokpasien_m', 'pasiendaftar_t.objectkelompokpasienlastfk', '=', 'kelompokpasien_m.id')
            ->leftJoin('pegawai_m', 'pasiendaftar_t.objectpegawaifk', '=', 'pegawai_m.id')
            ->leftJoin('rekanan_m', 'pasiendaftar_t.objectrekananfk', '=', 'rekanan_m.id')
            ->where('pasiendaftar_t.statusenabled', true)
            // ->where('pasiendaftar_t.objectkelompokpasienlastfk', '!=', 2)
            // ->where('departemen_m.id', '=', '18') // ambil data rawat jalan id departemen 18
            ->whereIn('departemen_m.id', ['18','3','24','27'])
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween('pasiendaftar_t.tglregistrasi', [
                    $tglAwal . ' 00:00:00',
                    $tglAkhir . ' 23:59:59'
                ]);
            })
            // ->distinct()
            ->select(
                'pasiendaftar_t.noregistrasi',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'kelompokpasien_m.kelompokpasien',
                'kelompokpasien_m.namaexternal as jenis_pembayaran',
                'rekanan_m.namarekanan',
                'pasiendaftar_t.tglregistrasi',
                'pasiendaftar_t.tglpulang',
                'ruangan_m.kodeeksternal',
                'ruangan_m.namaruangan',
                'pegawai_m.id as iddokter',
                'pegawai_m.namalengkap as namadokter',
                'pasiendaftar_t.norec as norec_pd',
                // 'antrianpasiendiperiksa_t.norec as norec_apd',
                'ruangan_m.id as idruangan',
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
            $details = array();
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
                    'pr.id as prid',
                    'pr.namaproduk',
                    'pp.jumlah',
                    'pp.produkfk',
                    'pp.hargajual',
                    'pp.hargadiscount',
                    'sp.nostruk',
                    'sp.tglstruk',
                    // 'sbm.nosbm',
                    // 'sp.norec as norec_sp',
                    'pp.jasa',
                    'ru.id as ruid',
                    'ru.namaruangan',
                    'sr.noresep',
                    'rusr.namaruangan as ruanganfarmasi',
                    'pgsr.namalengkap as penulisresep',
                    'pgdokter.namalengkap as namadokter_pp',
                    // 'kpBpjs.kelompokprodukbpjs as kelompokprodukbpjs',
                    'kls.id as klsid',
                    'kls.namakelas',
                    'pp.jasa',
                    'sp.totalharusdibayar',
                    'sp.totalprekanan',
                    'sppj.totalppenjamin',
                    'sp.totalbiayatambahan',
                    'pgsbm.namalengkap as namalengkapsbm',
                    // 'pp.aturanpakai',
                    // 'pp.iscito',
                    // 'pp.isparamedis'
                )
                // ->whereNull('pp.aturanpakai')
                ->where('pd.noregistrasi', '=', $noregistrasi)
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
                ->where('pd.noregistrasi', '=', $noregistrasi)
                ->get();
            if (count($pelayanan) > 0) {
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
                    $jasa = false;
                    $NamaDokter = '-';
                    $kodeDokter = '';
                    foreach ($pelayananpetugas as $hahaha) {
                        if ($hahaha->pelayananpasien == $item->norec) {
                            $NamaDokter = $hahaha->namalengkap;
                            $kodeDokter = $hahaha->iddokter;
                            if($NamaDokter != '-' ){
                                $jasa = true;
                            }
                            
                        }
                    }
                    
                    $harga = (float)$item->hargajual;
                    $diskon = (float)$item->hargadiscount;

                    $harga = (float) $item->hargajual;
                    $qty   = (float) $item->jumlah;

                    $subtotal = $harga * $qty;

                    $total_biaya += $subtotal;
                    $total_tagihan += $subtotal;
                    $total_pembayaran += $subtotal;

                    $detail = [
                        'kode_tindakan' => (string)$item->prid,
                        'nama_tindakan' => $item->namaproduk,
                        'harga_satuan' => $harga,
                        'qty_transaksi' => $item->jumlah,
                        'total_transaksi' => $item->hargajual * $item->jumlah,
                        'is_jasa' => $jasa,
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
            // $totals = DB::selectOne("
            //     SELECT
            //         SUM(
            //             (COALESCE(pp.hargajual,0) * COALESCE(pp.jumlah,0))
            //             + COALESCE(pp.jasa,0)
            //         ) AS total_biaya,

            //         SUM(
            //             (COALESCE(pp.hargajual,0) * COALESCE(pp.jumlah,0))
            //             + COALESCE(pp.jasa,0)
            //         ) FILTER (WHERE sp.nosbmlastfk IS NOT NULL) AS total_pembayaran,

            //         SUM(
            //             (COALESCE(pp.hargajual,0) * COALESCE(pp.jumlah,0))
            //             + COALESCE(pp.jasa,0)
            //         ) FILTER (WHERE pp.strukfk IS NOT NULL) AS total_tagihan

            //     FROM pasiendaftar_t pd
            //     JOIN antrianpasiendiperiksa_t apd
            //         ON apd.noregistrasifk = pd.norec
            //     JOIN pelayananpasien_t pp
            //         ON pp.noregistrasifk = apd.norec
            //     LEFT JOIN strukpelayanan_t sp
            //         ON sp.norec = pp.strukfk
            //     WHERE pd.noregistrasi = :noregistrasi
            //     AND apd.objectruanganfk = :idRuangan
            //     AND pp.produkfk NOT IN (402611)
            // ", [
            //     'noregistrasi' => $noregistrasi,
            //     'idRuangan' => $idRuangan
            // ]);

            $payload = array();
            $payload = [
                'nomor_transaksi' => $noregistrasi,
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
            // dd($payload);
            dispatch(new kirimToBod($payload))
                ->delay(now()->addSeconds($delay));

            $delay += 1;

            Log::info('Payload kirimKunjunganRawatJalan', $payload);
        }
        // dd($datas);
    }
}
