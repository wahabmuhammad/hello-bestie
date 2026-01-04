<?php

namespace App\Console\Commands;

use App\services\bodService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendRawatJalanBod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-rawat-jalan-bod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim data rawat jalan ke BOD RS AISYIYAH GROUP KUDUS';

    /**
     * Execute the console command.
     */

    protected bodService $bod;

    public function __construct(bodService $bod)
    {
        parent::__construct();
        $this->bod = $bod;
    }

    public function handle()
    {
        $pasiendaftar = DB::table('pasiendaftar_t')
            ->leftJoin('antrianpasiendiperiksa_t', 'pasiendaftar_t.norec', '=', 'antrianpasiendiperiksa_t.noregistrasifk')
            ->leftJoin('ruangan_m', 'antrianpasiendiperiksa_t.objectruanganfk', '=', 'ruangan_m.id')
            ->leftJoin('departemen_m', 'ruangan_m.objectdepartemenfk', '=', 'departemen_m.id')
            ->where('statusenabled', '=', 't')
            ->where('departemen_m.id', '=', '18') // ambil data rawat jalan id departemen 18
            ->select('pasiendaftar_t.norec', 'pasiendaftar_t.noregistrasi', 'antrianpasiendiperiksa_t.norec as norec_apd')
            ->whereBetween('tglregistrasi', [
                '2025-01-01 00:00:00',
                '2025-01-31 23:59:59'
            ])
            ->get();
        $details = array();
        foreach ($pasiendaftar as $pd) {
            $pelayanan = DB::table('pelayananpasien_t')
                ->where('noregistrasifk', '=', $pd->norec_apd)
                ->where('statusenabled', '=', 't')
                ->get();
        }
    }
}
