<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'config',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope pentru secțiunile active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pentru ordonare după sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Obține bannerele pentru secțiunea de tip slider sau banners_row
     */
    public function getBanners()
    {
        if (!in_array($this->type, ['slider', 'banners_row'])) {
            return collect();
        }

        $position = $this->config['position'] ?? 'slider';
        
        return Banner::active()
            ->byPosition($position)
            ->ordered()
            ->get();
    }

    /**
     * Obține produsele pentru secțiunea de tip products_grid
     */
    public function getProducts()
    {
        if ($this->type !== 'products_grid') {
            return collect();
        }

        $productIds = $this->config['product_ids'] ?? [];
        $limit = $this->config['limit'] ?? 8;

        if (empty($productIds)) {
            return Product::where('is_active', true)
                ->where('is_featured', true)
                ->take($limit)
                ->get();
        }

        return Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->take($limit)
            ->get();
    }

    /**
     * Obține categoriile pentru secțiunea de tip categories_grid
     */
    public function getCategories()
    {
        if ($this->type !== 'categories_grid') {
            return collect();
        }

        $categoryIds = $this->config['category_ids'] ?? [];
        $limit = $this->config['limit'] ?? 6;

        if (empty($categoryIds)) {
            return Category::where('is_active', true)
                ->take($limit)
                ->get();
        }

        return Category::whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->take($limit)
            ->get();
    }
}
