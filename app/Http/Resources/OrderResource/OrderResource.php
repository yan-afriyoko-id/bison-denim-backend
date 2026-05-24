<?php

namespace App\Http\Resources\OrderResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'queue_number' => $this->queue_number,
            'order_number' => $this->order_number,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,

            // Shipping address
            'shipping' => [
                'country' => $this->shipping_country,
                'first_name' => $this->shipping_first_name,
                'last_name' => $this->shipping_last_name,
                'address' => $this->shipping_address,
                'city' => $this->shipping_city,
                'province' => $this->shipping_province,
                'postal_code' => $this->shipping_postal_code,
                'label_place' => $this->shipping_label_place,
                'note_address' => $this->shipping_note_address,
                'province_id' => $this->shipping_province_id,
                'province_label' => $this->shipping_province_label,
                'city_id' => $this->shipping_city_id,
                'city_label' => $this->shipping_city_label,
                'district_id' => $this->shipping_district_id,
                'district_label' => $this->shipping_district_label,
                'sub_district_id' => $this->shipping_sub_district_id,
                'sub_district_label' => $this->shipping_sub_district_label,
            ],

            // Billing address
            'billing' => [
                'country' => $this->billing_country,
                'first_name' => $this->billing_first_name,
                'last_name' => $this->billing_last_name,
                'address' => $this->billing_address,
                'city' => $this->billing_city,
                'province' => $this->billing_province,
                'postal_code' => $this->billing_postal_code,
                'label_place' => $this->billing_label_place,
                'note_address' => $this->billing_note_address,
            ],

            // Courier information
            'courier' => [
                'agent' => $this->courier_agent,
                'service' => $this->courier_agent_service,
                'service_desc' => $this->courier_agent_service_desc,
                'estimate_delivered' => $this->courier_estimate_delivered,
                'resi_number' => $this->courier_resi_number,
                'cost' => $this->courier_cost,
            ],

            // Payment information
            'payment' => [
                'method' => $this->payment_method,
                'reference_code' => $this->payment_reference_code,
                'snap_token' => $this->payment_snap_token,
                'status' => $this->payment_status,
            ],

            // Order totals
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'points_used' => $this->points_used,
            'shipping_cost' => $this->shipping_cost,
            'product_protection_amount' => $this->product_protection_amount,
            'total_amount' => $this->total_amount,

            // Order notes
            'invoice_note' => $this->invoice_note,
            'delivery_order_note' => $this->delivery_order_note,

            // Status
            'status' => $this->status,

            // Relationships
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'order_items' => $this->whenLoaded('orderItems', function () use ($request) {
                $currentUserId = $request->user()?->id;

                return $this->orderItems->map(function ($item) use ($currentUserId) {
                    $review = $item->review;

                    $canEditReview = false;

                    if ($review && $currentUserId && $review->user_id === $currentUserId) {
                        $canEditReview = now()->lessThan(
                            $review->created_at->copy()->addHour()
                        );
                    }

                    return [
                        'id' => $item->id,
                        'product_id' => $item->fk_product_id,
                        'variant_id' => $item->fk_variant_id,
                        'product_name' => $item->product_name,
                        'product_image' => $item->product_image,
                        'sku' => $item->sku,
                        'variant_description' => $item->variant_description,
                        'qty' => $item->qty,
                        'actual_price' => $item->actual_price,
                        'discount_price' => $item->discount_price,
                        'purchase_price' => $item->purchase_price,
                        'product_protection_percent' => $item->product_protection_percent,
                        'product_protection_amount' => $item->product_protection_amount,
                        'subtotal' => $item->subtotal,
                        'note' => $item->note,
                        'review_id' => $item->review_id,
                        'review' => $review ? [
                            'id' => $review->id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'created_at' => $review->created_at?->toISOString(),
                            'can_edit' => $canEditReview,
                        ] : null,
                    ];
                });
            }),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
