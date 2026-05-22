<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    use HasFactory;

    /**
     * Atribute care pot fi completate în masă
     */
    protected $fillable = [
        'receipt_id',
        'product_id',
        'quantity',
        'purchase_price_without_vat',
        'vat_rate',
        'vat_value',
        'purchase_price_with_vat',
        'line_total_without_vat',
        'line_total_vat',
        'line_total_with_vat',
    ];

    /**
     * Casturi automate
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'purchase_price_without_vat' => 'decimal:2',
        'vat_rate' => 'integer',
        'vat_value' => 'decimal:2',
        'purchase_price_with_vat' => 'decimal:2',
        'line_total_without_vat' => 'decimal:2',
        'line_total_vat' => 'decimal:2',
        'line_total_with_vat' => 'decimal:2',
    ];

    /**
     * Relația cu recepția
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    /**
     * Relația cu produsul
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
