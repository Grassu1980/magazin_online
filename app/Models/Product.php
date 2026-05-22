<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'vat_rate',
        'price_without_vat',
        'special_price',
        'is_on_sale',
        'stock',
        'sku',
        'image',
        'is_active',
        'is_featured',
        'views',
        'sold_count',
        'purchase_price_without_vat',
        'price_with_vat',
        'promo_price',
        'promo_start',
        'promo_end',
    ];

    protected $casts = [
        'is_on_sale' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'vat_rate' => 'integer',
        'price_without_vat' => 'decimal:2',
        'special_price' => 'float',
        'stock' => 'integer',
        'purchase_price_without_vat' => 'decimal:2',
        'price_with_vat' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'promo_start' => 'datetime',
        'promo_end' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAȚII
    |--------------------------------------------------------------------------
    */

    /**
     * Relația cu categoria
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relația cu imaginile
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Relația cu comenzile
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    /**
     * Relația cu order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relația cu stocul
     */
    public function stockRecord()
    {
        return $this->hasOne(Stock::class);
    }

    /**
     * Relația cu stocul (alias pentru compatibilitate)
     */
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    /**
     * Relația cu recepțiile
     */
    public function receiptItems()
    {
        return $this->hasMany(ReceiptItem::class);
    }

    /**
     * Relația cu istoricul prețurilor
     */
    public function priceHistory()
    {
        return $this->hasMany(ProductPriceHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obține cantitatea din stoc
     */
    public function getStockQuantityAttribute()
    {
        return $this->stockRecord ? $this->stockRecord->quantity : 0;
    }

    public function getImagesAttribute($value)
    {
        if ($this->relationLoaded('images')) {
            return $this->getRelation('images');
        }

        return $this->images()->get();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORI
    |--------------------------------------------------------------------------
    */

    // Preț final (cu discount dacă există)
    public function getFinalPriceAttribute()
    {
        if ($this->is_on_sale && $this->special_price > 0) {
            return $this->special_price;
        }

        return $this->price;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->final_price;
    }

    // Procent reducere
    public function getDiscountPercentageAttribute()
    {
        if ($this->is_on_sale && $this->special_price > 0) {
            return round((($this->price - $this->special_price) / $this->price) * 100);
        }

        return 0;
    }

    // Toate imaginile (principală + multiple)
    public function getAllImagesAttribute()
    {
        $images = [];

        // Imagine principală
        if (!empty($this->image)) {
            $images[] = $this->image;
        }

        // Imagini multiple
        if ($this->images->count() > 0) {
            foreach ($this->images as $img) {
                if (!empty($img->image_path)) {
                    $images[] = $img->image_path;
                }
            }
        }

        return $images;
    }

    // Adaos comercial (%)
    public function getMarkupPercentageAttribute()
    {
        if ($this->purchase_price_without_vat && $this->purchase_price_without_vat > 0) {
            return (($this->price_without_vat - $this->purchase_price_without_vat) / $this->purchase_price_without_vat) * 100;
        }
        return 0;
    }

    // Profit brut
    public function getGrossProfitAttribute()
    {
        if ($this->price_without_vat && $this->purchase_price_without_vat) {
            return $this->price_without_vat - $this->purchase_price_without_vat;
        }
        return 0;
    }

    // Verifică dacă promoția este activă
    public function isPromoActive()
    {
        if (!$this->promo_price || !$this->promo_start || !$this->promo_end) {
            return false;
        }
        
        $now = now();
        return $now->between($this->promo_start, $this->promo_end);
    }

    /*
    |--------------------------------------------------------------------------
    | METODE LOGICE
    |--------------------------------------------------------------------------
    */

    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function decrementStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
        }
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
