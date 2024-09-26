<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;

class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(string $id): JsonResponse
    {
        try {
            $status = $this->voucherService->deleteVoucher($id);
            return response()->json(["status" => $status], 200);
        } catch (Exception $exception) {
            return response()->json(["status" => $status, "message" => $exception->getMessage()], 400);
        }
    }
}
