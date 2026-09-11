<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $products = $this->whenLoaded('products', function () {
            $this->products->each(fn ($product) => $product->setRelation('transaction', $this->resource));

            return TransactionProductResource::collection($this->products);
        });

        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => round($this->amount),
            'commission' => round($this->commission),
            'notes' => $this->notes,
            'attachment' => $this->attachment,
            'balance_before_transaction' => round($this->balance_before_transaction),
            'balance_after_transaction' => round($this->balance_after_transaction),
            'debt_before' => $this->debtLog ? $this->debtLog->debt_before : null,
            'debt_after' => $this->debtLog ? $this->debtLog->debt_after : null,
            'client_id' => $this->client_id,
            'product_id' => $this->product_id,
            'payment_way_id' => $this->payment_way_id,
            'quantity' => $this->quantity,
            'paymentWay' => new PaymentWayResource($this->whenLoaded('paymentWay')),
            'payment_splits' => $this->whenLoaded('paymentSplits', fn () => $this->paymentSplits->map(fn ($payment) => [
                'id' => $payment->id,
                'payment_way_id' => $payment->payment_way_id,
                'payment_way_name' => $payment->paymentWay?->name,
                'amount' => round($payment->amount, 2),
                'balance_before_transaction' => round($payment->balance_before_transaction, 2),
                'balance_after_transaction' => round($payment->balance_after_transaction, 2),
            ])->values()),
            'payment_way_split_amount' => $this->when(isset($this->payment_way_split_amount), fn () => round($this->payment_way_split_amount, 2)),
            'payment_way_split_commission' => $this->when(isset($this->payment_way_split_commission), fn () => round($this->payment_way_split_commission, 2)),
            'payment_way_split_balance_before' => $this->when(isset($this->payment_way_split_balance_before), fn () => round($this->payment_way_split_balance_before, 2)),
            'payment_way_split_balance_after' => $this->when(isset($this->payment_way_split_balance_after), fn () => round($this->payment_way_split_balance_after, 2)),
            'creator' => new UserResource($this->creator),
            'logs' => TransactionLogResource::collection($this->whenLoaded('logs')),
            'products' => $products,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'is_edited' => $this->updated_at && $this->created_at && $this->updated_at->ne($this->created_at),
            'client' => new ClientResource($this->whenLoaded('client')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'whatsapp' => $this->when(isset($this->whatsapp), $this->whatsapp),
            // 'installmentPayment' => new InstallmentPaymentResource($this->whenLoaded('installmentPayment')),
            'installmentPayment' => InstallmentPaymentResource::collection($this->whenLoaded('installmentPayment')),

        ];
    }
}
