<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    use HasFactory;

    protected $table = 'product_price_history';

    protected $fillable = [
        'product_id',
        'old_price_without_vat',
        'new_price_without_vat',
        'old_price_with_vat',
        'new_price_with_vat',
        'old_vat_rate',
        'new_vat_rate',
        'changed_by',
    ];

    protected $casts = [
        'old_price_without_vat' => 'decimal:2',
        'new_price_without_vat' => 'decimal:2',
        'old_price_with_vat' => 'decimal:2',
        'new_price_with_vat' => 'decimal:2',
        'old_vat_rate' => 'integer',
        'new_vat_rate' => 'integer',
    ];

    /**
     * Relația cu produsul
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relația cu utilizatorul care a modificat prețul
     */
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
