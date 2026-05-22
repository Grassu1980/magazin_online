<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    /**
     * Atribute care pot fi completate în masă
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'cost_price',
        'selling_price',
        'location',
    ];

    /**
     * Relația cu produsul
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relația cu istoricul stocului
     */
    public function histories()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Actualizează stocul
     * 
     * @param int $quantity
     * @param string $type
     * @param string $reference
     * @return bool
     */
    public function updateStock($quantity, $type, $reference = null)
    {
        $oldQuantity = $this->quantity;
        
        if ($type === 'in') {
            $this->quantity += $quantity;
        } elseif ($type === 'out') {
            $this->quantity -= $quantity;
        }

        $this->save();

        // Salvează în istoric
        StockHistory::create([
            'stock_id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $quantity,
            'type' => $type,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $this->quantity,
            'reference' => $reference,
        ]);

        return true;
    }
}
