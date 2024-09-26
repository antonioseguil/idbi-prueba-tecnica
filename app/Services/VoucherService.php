<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Jobs\Vouchers\ProcessVoucherLinesJob;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    private function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $fullIdVoucher = explode('-', (string) $xml->xpath('//cbc:ID')[0]);

        $voucherSerie = trim($fullIdVoucher[0]);
        $voucherNumber = trim($fullIdVoucher[1]);
        $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'voucher_serie' => $voucherSerie,
            'voucher_number' => $voucherNumber,
            'voucher_type_id' => $voucherType,
            'currency' => $currency,
            'user_id' => $user->id,
        ]);

        $voucher->save();

        $invoiceLines = $xml->xpath('//cac:InvoiceLine');

        if ($invoiceLines) {
            foreach ($invoiceLines as $invoiceLine) {
                $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
                $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
                $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

                dispatch(new ProcessVoucherLinesJob($name, $quantity, $unitPrice, $voucher->id));
            }
        }

        return $voucher;
    }

    public function deleteVoucher(string $voucherId): bool
    {
        $voucher = Voucher::find($voucherId);

        if (!$voucher) {
            return false;
        }

        $voucher->delete();

        return true;
    }
}
