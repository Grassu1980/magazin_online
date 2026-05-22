<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;

/**
 * Serviciu pentru gestionarea stocurilor
 */
class StockService
{
    /**
     * Obține sau creează stocul pentru un produs
     * 
     * @param Product $product
     * @return Stock
     */
    public function getOrCreateStock(Product $product)
    {
        $stock = Stock::where('product_id', $product->id)->first();
        
        if (!$stock) {
            $stock = Stock::create([
                'product_id' => $product->id,
                'quantity' => 0,
                'cost_price' => $product->price,
                'selling_price' => $product->price,
            ]);
        }
        
        return $stock;
    }

    /**
     * Adaugă stoc (intrare)
     * 
     * @param Product $product
     * @param int $quantity
     * @param string $reference
     * @param float $costPrice
     * @return bool
     */
    public function addStock(Product $product, $quantity, $reference = null, $costPrice = null)
    {
        $stock = $this->getOrCreateStock($product);
        
        if ($costPrice) {
            $stock->cost_price = $costPrice;
        }
        
        return $stock->updateStock($quantity, 'in', $reference);
    }

    /**
     * Scoate stoc (ieșire)
     * 
     * @param Product $product
     * @param int $quantity
     * @param string $reference
     * @return bool
     */
    public function removeStock(Product $product, $quantity, $reference = null)
    {
        $stock = $this->getOrCreateStock($product);
        
        if ($stock->quantity < $quantity) {
            throw new \Exception('Stoc insuficient');
        }
        
        return $stock->updateStock($quantity, 'out', $reference);
    }

    /**
     * Obține istoricul stocului pentru un produs
     * 
     * @param Product $product
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStockHistory(Product $product)
    {
        $stock = $this->getOrCreateStock($product);
        return $stock->histories()->latest()->get();
    }

    /**
     * Verifică dacă produsul este în stoc
     * 
     * @param Product $product
     * @param int $quantity
     * @return bool
     */
    public function isInStock(Product $product, $quantity = 1)
    {
        $stock = $this->getOrCreateStock($product);
        return $stock->quantity >= $quantity;
    }

    /**
     * Obține stocul curent
     * 
     * @param Product $product
     * @return int
     */
    public function getStockQuantity(Product $product)
    {
        $stock = $this->getOrCreateStock($product);
        return $stock->quantity;
    }

    /**
     * Actualizează prețul de cost
     * 
     * @param Product $product
     * @param float $costPrice
     * @return bool
     */
    public function updateCostPrice(Product $product, $costPrice)
    {
        $stock = $this->getOrCreateStock($product);
        $stock->cost_price = $costPrice;
        return $stock->save();
    }

    /**
     * Actualizează prețul de vânzare
     * 
     * @param Product $product
     * @param float $sellingPrice
     * @return bool
     */
    public function updateSellingPrice(Product $product, $sellingPrice)
    {
        $stock = $this->getOrCreateStock($product);
        $stock->selling_price = $sellingPrice;
        return $stock->save();
    }
}
