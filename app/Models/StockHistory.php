<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    /**
     * Atribute care pot fi completate în masă
     */
    protected $fillable = [
        'stock_id',
        'product_id',
        'quantity',
        'type',
        'old_quantity',
        'new_quantity',
        'reference',
        'notes',
    ];

    /**
     * Relația cu stocul
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Relația cu produsul
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope pentru intrări
     */
    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope pentru ieșiri
     */
    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }
}
