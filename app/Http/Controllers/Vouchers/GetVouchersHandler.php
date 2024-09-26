<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\Voucher;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;

class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): JsonResponse
    {
        $user = auth()->user();
        try {
            $vouchers = $this->voucherService->getVouchers(
                page: $request->query('page'),
                paginate: $request->query('paginate'),
                serie: $request->query('serie'),
                number: $request->query('number'),
                dateStart: $request->query('date_start'),
                dateEnd: $request->query('date_end'),
                userId: $user->id
            );

            return response()->json($vouchers, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
            ], 400);
        }

    }
}
