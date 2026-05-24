<?php

namespace App\Services\Voucher;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class VoucherValidationService
{
    public function validate(
        Voucher $voucher,
        $user,
        array $productIds,
        int $subTotal
    ): array {

        $now = now();
        if (in_array($voucher->status, ['DRAFT', 'INACTIVE'])) {
            return $this->fail('Voucher tidak aktif');
        }
        if ($voucher->start_date && $voucher->start_date > $now) {
            return $this->fail('Voucher belum berlaku');
        }

        if ($voucher->end_date && $voucher->end_date < $now) {
            return $this->fail('Voucher sudah kadaluarsa');
        }
        if ($subTotal < $voucher->min_purchase) {
            return $this->fail(
                'Minimum belanja Rp ' . number_format($voucher->min_purchase)
            );
        }
        if ($voucher->limit_user !== null) {
            $paidOrdersCount = Order::where('fk_voucher_id', $voucher->id)
                ->where('payment_status', 'PAID')
                ->whereNull('deleted_at')
                ->count();

            if ($paidOrdersCount >= $voucher->limit_user) {
                return $this->fail('Voucher melebihi batas penggunaan');
            }
        }
        if ($user) {
            $hasUsed = Order::where('fk_user_id', $user->id)
                ->where('fk_voucher_id', $voucher->id)
                ->where('payment_status', 'PAID')
                ->whereNull('deleted_at')
                ->exists();

            if ($hasUsed) {
                return $this->fail('Voucher sudah pernah digunakan');
            }
            $pendingOrder = Order::where('fk_user_id', $user->id)
                ->where('fk_voucher_id', $voucher->id)
                ->where('payment_status', 'PENDING')
                ->where('status', '!=', 'CANCELLED')
                ->whereNull('deleted_at')
                ->exists();

            if ($pendingOrder) {
                return $this->fail(
                    'Voucher sedang digunakan pada transaksi yang belum diproses'
                );
            }
        }
        if ($voucher->categories()->exists()) {

            $categoryIds = DB::table('category_products')
                ->whereIn('fk_product_id', $productIds)
                ->pluck('fk_category_id')
                ->unique();

            $voucherCategoryIds = $voucher->categories
                ->pluck('id');

            if ($voucherCategoryIds->intersect($categoryIds)->isEmpty()) {
                return $this->fail('Voucher tidak berlaku untuk produk ini');
            }
        }
        $discount = 0;

        if ($voucher->discount_type === 'FIXED') {
            $discount = (int) $voucher->discount_value;
        }

        if ($voucher->discount_type === 'PERCENTAGE') {
            $discount = (int) floor(
                $subTotal * ($voucher->discount_value / 100)
            );
        }

        $discount = min($discount, $subTotal);

        return [
            'valid' => true,
            'message' => null,
            'discount' => $discount,
        ];
    }

    private function fail(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
            'discount' => 0,
        ];
    }
}
