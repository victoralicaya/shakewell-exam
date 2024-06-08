<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    private $user;
    private $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->user = auth()->user();
        $this->voucherService = $voucherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $vouchers = $this->voucherService->getAllVouchers($this->user);

            return response()->json([
                'count' => $vouchers->count(),
                'data' => VoucherResource::collection($vouchers)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve vouchers.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        try {
            $voucher = $this->voucherService->createVoucherForUser($this->user);

            return response()->json([
                'message' => 'Voucher successfully created.',
                'data' => new VoucherResource($voucher)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Voucher creation failed.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        try {
            $this->voucherService->deleteVoucher($this->user, $voucher);

            return response()->json([
                'message' => 'Voucher successfully deleted.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Deleting voucher failed.",
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
