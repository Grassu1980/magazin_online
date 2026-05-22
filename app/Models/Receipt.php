<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    /**
     * Atribute care pot fi completate în masă
     */
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'receipt_date',
        'total_without_vat',
        'total_vat',
        'total_with_vat',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Casturi automate
     */
    protected $casts = [
        'invoice_date' => 'date',
        'receipt_date' => 'date',
        'total_without_vat' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'total_with_vat' => 'decimal:2',
    ];

    /**
     * Relația cu furnizorul
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relația cu utilizatorul care a creat recepția
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relația cu utilizatorul care a actualizat recepția
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relația cu produsele din recepție
     */
    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }
}
