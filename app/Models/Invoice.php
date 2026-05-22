<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Atribute care pot fi completate în masă
     */
    protected $fillable = [
        'invoice_number',
        'order_id',
        'client_name',
        'client_email',
        'subtotal',
        'tva_amount',
        'tva_rate',
        'total',
        'pdf_path',
        'xml_path',
        'efactura_status',
        'efactura_message',
        'invoice_type',
        'company_name',
        'company_cui',
        'company_reg',
        'company_address',
        'company_iban',
    ];

    /**
     * Relația cu comanda
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Generează numărul următor de factură
     */
    public static function generateInvoiceNumber($prefix, $startNumber)
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        
        if ($lastInvoice) {
            // Extrage numărul din ultima factură
            $lastNumber = (int) str_replace($prefix, '', $lastInvoice->invoice_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = $startNumber;
        }
        
        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
