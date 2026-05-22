<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPriceHistory;
use Illuminate\Support\Facades\Auth;

class PriceService
{
    /**
     * Calculează prețul fără TVA din prețul cu TVA
     */
    public function calculatePriceWithoutVat(float $priceWithVat, int $vatRate): float
    {
        return $priceWithVat / (1 + $vatRate / 100);
    }

    /**
     * Calculează prețul cu TVA din prețul fără TVA
     */
    public function calculatePriceWithVat(float $priceWithoutVat, int $vatRate): float
    {
        return $priceWithoutVat * (1 + $vatRate / 100);
    }

    /**
     * Calculează adaosul comercial (%)
     */
    public function calculateMarkupPercentage(float $priceWithoutVat, float $purchasePriceWithoutVat): float
    {
        if ($purchasePriceWithoutVat > 0) {
            return (($priceWithoutVat - $purchasePriceWithoutVat) / $purchasePriceWithoutVat) * 100;
        }
        return 0;
    }

    /**
     * Calculează prețul fără TVA din adaos comercial
     */
    public function calculatePriceFromMarkup(float $purchasePriceWithoutVat, float $markupPercentage): float
    {
        return $purchasePriceWithoutVat * (1 + $markupPercentage / 100);
    }

    /**
     * Calculează profitul brut
     */
    public function calculateGrossProfit(float $priceWithoutVat, float $purchasePriceWithoutVat): float
    {
        return $priceWithoutVat - $purchasePriceWithoutVat;
    }

    /**
     * Salvează istoricul prețurilor la modificare
     */
    public function savePriceHistory(Product $product, array $oldData, array $newData): void
    {
        // Verifică dacă prețul s-a schimbat
        $priceChanged = 
            $oldData['price_without_vat'] != $newData['price_without_vat'] ||
            $oldData['price_with_vat'] != $newData['price_with_vat'] ||
            $oldData['vat_rate'] != $newData['vat_rate'];

        if ($priceChanged) {
            ProductPriceHistory::create([
                'product_id' => $product->id,
                'old_price_without_vat' => $oldData['price_without_vat'],
                'new_price_without_vat' => $newData['price_without_vat'],
                'old_price_with_vat' => $oldData['price_with_vat'],
                'new_price_with_vat' => $newData['price_with_vat'],
                'old_vat_rate' => $oldData['vat_rate'],
                'new_vat_rate' => $newData['vat_rate'],
                'changed_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Procesează datele prețurilor pentru salvare
     */
    public function processPriceData(array $data): array
    {
        $vatRate = $data['vat_rate'] ?? 19;
        $purchasePriceWithoutVat = $data['purchase_price_without_vat'] ?? 0;
        
        // Dacă este introdus prețul cu TVA, calculează prețul fără TVA
        if (isset($data['price_with_vat']) && !isset($data['price_without_vat'])) {
            $data['price_without_vat'] = $this->calculatePriceWithoutVat($data['price_with_vat'], $vatRate);
        }
        
        // Dacă este introdus prețul fără TVA, calculează prețul cu TVA
        if (isset($data['price_without_vat']) && !isset($data['price_with_vat'])) {
            $data['price_with_vat'] = $this->calculatePriceWithVat($data['price_without_vat'], $vatRate);
        }
        
        // Dacă este introdus adaosul, calculează prețul
        if (isset($data['markup_percentage']) && $purchasePriceWithoutVat > 0) {
            $data['price_without_vat'] = $this->calculatePriceFromMarkup($purchasePriceWithoutVat, $data['markup_percentage']);
            $data['price_with_vat'] = $this->calculatePriceWithVat($data['price_without_vat'], $vatRate);
        }
        
        return $data;
    }
}
