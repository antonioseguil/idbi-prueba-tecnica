<?php

namespace App\Jobs\Vouchers;

use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SimpleXMLElement;

class ProcessVoucherLinesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @param array|bool|null $invoceLines
     * @param string $voucherId
     */
    public function __construct(
        private Voucher $voucher,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $xml = new SimpleXMLElement($this->voucher->xml_content);

        $invoiceLines = $xml->xpath('//cac:InvoiceLine');

        if ($invoiceLines) {
            foreach ($invoiceLines as $invoiceLine) {
                $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
                $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
                $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

                $voucherLine = new VoucherLine([
                    'name' => $name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'voucher_id' => $this->voucher->id,
                ]);

                $voucherLine->save();
            }
        }
    }
}
