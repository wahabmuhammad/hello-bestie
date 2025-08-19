<?php

namespace App\Jobs;

use App\services\fonnteServices;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class kirimPesanFonnte implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone, $pesan;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $pesan)
    {
        $this->phone = $phone;
        $this->pesan = $pesan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        fonnteServices::sendText($this->phone, $this->pesan);
    }
}
