<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetVoucherHandler
{

    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $response = $this->voucherService->getVoucherTotal($user->id);

            return response()->json(["data" => $response], 200);
        } catch (Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 400);
        }
    }
}
