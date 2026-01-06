<?php

namespace App\Jobs;

use App\services\bodService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class KirimToBodRanap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    public int $tries = 3;
    public int $timeout = 60;
    /**
     * Create a new job instance.
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            bodService::sendRawatInap($this->payload);

            Log::info('Sukses kirimToBod', [
                'nomor_transaksi' => $this->payload['nomor_transaksi'] ?? null,
            ]);
        } catch (\Throwable $e) {

            Log::error('Gagal kirimToBod', [
                'nomor_transaksi' => $this->payload['nomor_transaksi'] ?? null,
                'message' => $e->getMessage(),
            ]);

            // lempar ulang supaya queue tahu ini gagal
            throw $e;
        }
    }

    /**
     * Jeda retry
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    /**
     * Jika sudah benar-benar gagal
     */
    public function failed(\Throwable $e): void
    {
        Log::critical('FINAL FAILED kirimToBod', [
            'nomor_transaksi' => $this->payload['nomor_transaksi'] ?? null,
            'error' => $e->getMessage(),
        ]);
    }
}
