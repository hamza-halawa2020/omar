<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IphoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_type' => $this->device_type,
            'device_details' => $this->device_details,
            'purchase_price_sar' => $this->purchase_price_sar,
            'currency' => $this->currency,
            'purchase_price_egp' => $this->purchase_price_egp,
            'extra_expenses' => $this->extra_expenses,
            'total_purchase_with_expenses' => $this->total_purchase_with_expenses,
            'sale_price_egp' => $this->sale_price_egp,
            'net_profit_after_sale' => $this->net_profit_after_sale,
            'status' => $this->status,
            'financial_summary' => $this->financialSummary(),
            'sale_client' => $this->saleClientData(),
            'logs_count' => $this->whenLoaded('logs', fn () => $this->logs->count()),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function financialSummary(): array
    {
        $logs = $this->whenLoaded('logs', fn () => $this->logs, collect());
        $baseCost = (float) $this->total_purchase_with_expenses;
        $salesTotal = (float) $logs->where('action_type', 'sale')->sum('amount');
        $returnsTotal = (float) $logs->where('action_type', 'return')->sum('amount');
        $maintenanceCost = (float) $logs->whereIn('action_type', ['maintenance', 'expense'])->sum('amount');
        $totalCost = $baseCost + $maintenanceCost;
        $netProfit = $salesTotal - $returnsTotal - $totalCost;

        return [
            'base_cost' => number_format($baseCost, 2, '.', ''),
            'sales_total' => number_format($salesTotal, 2, '.', ''),
            'returns_total' => number_format($returnsTotal, 2, '.', ''),
            'maintenance_cost' => number_format($maintenanceCost, 2, '.', ''),
            'total_cost' => number_format($totalCost, 2, '.', ''),
            'net_profit' => number_format($netProfit, 2, '.', ''),
        ];
    }

    private function saleClientData(): ?array
    {
        $logs = $this->whenLoaded('logs', fn () => $this->logs, collect());
        $saleLog = $logs->where('action_type', 'sale')->whereNotNull('client_id')->first();

        if (! $saleLog) {
            return null;
        }

        return [
            'id' => $saleLog->client_id,
            'name' => optional($saleLog->client)->name,
        ];
    }
}
