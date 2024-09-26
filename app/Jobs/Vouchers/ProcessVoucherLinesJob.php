<?php

namespace App\Jobs\Vouchers;

use App\Models\VoucherLine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVoucherLinesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @param array|bool|null $invoceLines
     * @param string $voucherId
     */
    public function __construct(
        private string $name,
        private float $quantity,
        private float $unitPrice,
        private string $voucherId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $voucherLine = new VoucherLine([
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'voucher_id' => $this->voucherId,
        ]);

        $voucherLine->save();
    }
}
