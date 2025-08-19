<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class prosesNotifikasiPasienKontrol implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tglAkhir, $tglAwal;
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
        // Ambil data kontrol dan dokter
        $datas = DB::table('emrpasiend_t as kontrol')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'kontrol.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->leftJoin(
                DB::raw("(SELECT emrpasienfk, split_part(value, '~', 2) as namadokter 
                    FROM emrpasiend_t 
                    WHERE emrdfk = '43000006') as dokter"),
                'dokter.emrpasienfk',
                '=',
                'kontrol.emrpasienfk'
            )
            ->leftJoin(
                DB::raw("(SELECT emrpasienfk, value as namaruangan 
                    FROM emrpasiend_t 
                    WHERE emrdfk = '43000002') as ruangan"),
                'ruangan.emrpasienfk',
                '=',
                'kontrol.emrpasienfk'
            )
            ->where('kontrol.emrfk', '=', '210047')
            ->where('kontrol.emrdfk', '=', '43000003')
            ->when($this->tglAwal && $this->tglAkhir, function ($query) {
                $query->whereBetween(DB::raw("TO_TIMESTAMP(kontrol.value, 'YYYY-MM-DD HH24:MI')"), [
                    $this->tglAwal . ' 00:00',
                    $this->tglAkhir . ' 23:59'
                ]);
            })
            ->select(
                'ruangan.namaruangan',
                'kontrol.value as tglkontrol',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasien_m.nohp',
                'dokter.namadokter'
            )
            ->orderBy('kontrol.value', 'asc')
            ->get();

        Carbon::setLocale('id');
        $delay = 0;
        foreach ($datas as $data) {
            if ($data->tglkontrol) {
                $carbon = Carbon::parse($data->tglkontrol);
                $data->harikontrol = $carbon->translatedFormat('l'); // Contoh: Senin
                $data->formatTgl = $carbon->translatedFormat('d F Y'); // Contoh: 31 Juli 2025
            } else {
                $data->harikontrol = '-';
                $data->formatTgl = '-';
            }

            // Format nama dokter: fallback ke nama pegawai jika kosong
            $namadokter = $data->namadokter ?: '-';

            $pesan = "Assalamu'alaikum Wr. Wb.\n\n"
                . "Kepada Yth. *" . trim($data->namapasien) . "*\n\n"
                . "Kami dari RS Sarkies 'Aisyiyah Kudus ingin menginformasikan jadwal kontrol pada:\n\n"
                . "Hari, tgl   : " . trim($data->harikontrol) . ", " . trim($data->formatTgl) . "\n"
                . "Poliklinik  : " . trim($data->namaruangan) . "\n"
                . "Dokter     : " . trim($namadokter) . "\n"
                . "*Jadwal dokter bisa berubah sewaktu-waktu*\n\n"
                . "- Jika pembiayaan dengan BPJS, pendaftaran melalui aplikasi Mobile JKN, maksimal dilakukan H-1 dari hari pemeriksaan\n\n"
                . "Harap membawa persyaratan berikut ini:\n"
                . "1. Surat kontrol\n"
                . "2. Resume medis (bagi pasien rawat inap)\n\n"
                . "Demikian informasi yang dapat kami sampaikan, atas perhatiannya kami sampaikan terimakasih.\n\n"
                . "Salam sehat.\n\n"
                . "*Perihal jadwal dokter bisa klik dan ikuti link dibawah ini👇🏻.*\n"
                . "https://whatsapp.com/channel/0029Vamy8ZSDeON9NVKWcb1K\n\n"
                . "Wassalamu’alaikum Wr. Wb.";

            $phone = '0' . ltrim($data->nohp, '0');
            // $phone = '081215837977';

            dispatch(new kirimPesanFonnte($phone, $pesan))
                ->delay(now()->addSeconds($delay));

            $delay += 2;
        }
    }
}
