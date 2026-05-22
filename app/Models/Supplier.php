<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cui',
        'reg_com',
        'address',
        'city',
        'phone',
        'email',
        'contact_person',
        'is_active',
        'tva_status',
        'tva_valid_from',
        'tva_valid_to',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tva_valid_from' => 'date',
        'tva_valid_to' => 'date',
    ];

    /**
     * Relația cu recepțiile
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
