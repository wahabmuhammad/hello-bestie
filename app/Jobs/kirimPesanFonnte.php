<?php

namespace App\Jobs;

use App\services\fonnteServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class kirimPesanFonnte implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(public $phone, public $pesan)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        fonnteServices::sendText($this->phone, $this->pesan);
        sleep(2);
    }
}
