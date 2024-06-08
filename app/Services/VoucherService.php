<?php

namespace App\Services;

use App\Models\Voucher;
use Illuminate\Support\Str;

class VoucherService
{
    public function createVoucherForUser($user)
    {
        $this->voucherLimitReached($user);

        $code = $this->generateVoucherCode();

        return $this->storeVoucher($user, $code);
    }

    private function voucherLimitReached($user)
    {
        $count = $user->vouchers()->count();

        if ($count >= 10) {
            throw new \RuntimeException('Voucher limit reached. You can only create a maximum of 10 vouchers.');
        }
    }

    public function storeVoucher($user, $code)
    {
        return Voucher::create([
            'user_id' => $user->id,
            'code' => $code
        ]);
    }

    public function generateVoucherCode()
    {
        do {
            $code = Str::upper(Str::random(5));
            $voucher = Voucher::where('code', $code)->first();
        } while ($voucher);

        return $code;
    }

    public function getAllVouchers($user)
    {
        return Voucher::where('user_id', $user->id)->get();
    }

    public function deleteVoucher($user, $voucher)
    {
        if ($voucher && ($voucher->user_id !== $user->id)) {
            throw new \RuntimeException("You cannot delete a voucher that doesn't belong to you.");
        }

        $voucher->delete();
    }
}
