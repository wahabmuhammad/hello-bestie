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
    protected $tglAkhir, $tglAwal, $idDokter, $idRuangan;
    /**
     * Create a new job instance.
     */
    public function __construct($tglAwal, $tglAkhir, $idDokter, $idRuangan)
    {
        $this->tglAwal = $tglAwal;
        $this->tglAkhir = $tglAkhir;
        $this->idDokter = $idDokter;
        $this->idRuangan = $idRuangan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ambil data kontrol dan dokter
        $datas = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->leftJoin('emrpasiend_t as ed', function ($join) {
                $join->on('ed.emrpasienfk', '=', 'emrpasiend_t.emrpasienfk')
                    ->where('ed.emrdfk', '=', 43000006); // dokter
            })
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000003') // tgl kontrol
            ->when($this->idRuangan, function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('emrpasiend_t as er')
                        ->whereColumn('er.emrpasienfk', 'emrpasiend_t.emrpasienfk')
                        ->where('er.emrdfk', 43000002) // ruangan
                        ->where(DB::raw("split_part(er.value, '~', 1)"), '=', $this->idRuangan);
                });
            })
            ->when($this->idDokter, function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('emrpasiend_t as ed2')
                        ->whereColumn('ed2.emrpasienfk', 'emrpasiend_t.emrpasienfk')
                        ->where('ed2.emrdfk', 43000006) // dokter
                        ->where(DB::raw("split_part(ed2.value, '~', 1)"), '=', $this->idDokter);
                });
            })
            ->when($this->tglAwal && $this->tglAkhir, function ($query) {
                $query->whereBetween(DB::raw("TO_TIMESTAMP(emrpasiend_t.value, 'YYYY-MM-DD HH24:MI')"), [
                    $this->tglAwal . ' 00:00',
                    $this->tglAkhir . ' 23:59'
                ]);
            }, function ($query) {
                // default hari ini kalau tgl tidak dikirim
                $today = now()->format('Y-m-d');
                $query->whereBetween(DB::raw("TO_TIMESTAMP(emrpasiend_t.value, 'YYYY-MM-DD HH24:MI')"), [
                    $today . ' 00:00',
                    $today . ' 23:59'
                ]);
            })
            ->select(
                // 'pegawai_m.namalengkap as namapegawai',
                'emrpasien_t.namaruangan',
                // 'emrpasien_t.noemr',
                'emrpasiend_t.value as tglkontrol',
                // 'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasien_m.nohp',
                // 'pasien_m.tgllahir',
                DB::raw("COALESCE(split_part(ed.value, '~', 2), '-') as namadokter")
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->get();
        // dd($datas);
        Carbon::setLocale('id');
        $delay = 0;
        foreach ($datas as $data) {
            if ($data->tglkontrol) {
                $carbon = Carbon::parse($data->tglkontrol);
                $data->harikontrol = $carbon->translatedFormat('l');
                $data->formatTgl = $carbon->translatedFormat('d F Y');
            } else {
                $data->harikontrol = '-';
                $data->formatTgl = '-';
            }

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
                . "Untuk pasien selesai dari rawat inap mengikuti jadwal kontrol sesuai yang tertera di resume medis.\n\n"
                . "Demikian informasi yang dapat kami sampaikan, atas perhatiannya kami sampaikan terimakasih.\n\n"
                . "Salam sehat.\n\n"
                . "*Perihal jadwal dokter bisa klik dan ikuti link dibawah ini👇🏻.*\n"
                . "https://whatsapp.com/channel/0029Vamy8ZSDeON9NVKWcb1K\n\n"
                . "Wassalamu’alaikum Wr. Wb.";

            $phone = '0' . ltrim($data->nohp, '0');
            // $phone = '081215837977'; // nomor untuk testing


            dispatch(new kirimPesanFonnte($phone, $pesan))
                ->delay(now()->addSeconds($delay));

            $delay += 2;
        }
    }
}
