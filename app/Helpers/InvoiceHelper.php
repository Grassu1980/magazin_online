<?php

namespace App\Helpers;

/**
 * Helper pentru setările de facturare și firmă
 */
class InvoiceHelper
{
    /**
     * Returnează toate setările de facturare
     * 
     * @return array
     */
    public static function getInvoiceSettings()
    {
        return [
            'company_name' => setting('company_name'),
            'company_reg_number' => setting('company_reg_number'),
            'company_trade_number' => setting('company_trade_number'),
            'company_address' => setting('company_address'),
            'company_iban' => setting('company_iban'),
            'company_bank' => setting('company_bank'),
            'company_email' => setting('company_email'),
            'company_phone' => setting('company_phone'),
            'invoice_prefix' => setting('invoice_prefix', 'INV-'),
            'invoice_start_number' => setting('invoice_start_number', 1001),
            'invoice_footer_text' => setting('invoice_footer_text'),
            'tva_rate' => setting('tva_rate', 19),
        ];
    }

    /**
     * Returnează toate setările firmei
     * 
     * @return array
     */
    public static function getCompanySettings()
    {
        return [
            'company_name' => setting('company_name'),
            'company_reg_number' => setting('company_reg_number'),
            'company_trade_number' => setting('company_trade_number'),
            'company_address' => setting('company_address'),
            'company_iban' => setting('company_iban'),
            'company_bank' => setting('company_bank'),
            'company_email' => setting('company_email'),
            'company_phone' => setting('company_phone'),
            'efactura_api_key' => setting('efactura_api_key'),
            'efactura_client_id' => setting('efactura_client_id'),
            'efactura_client_secret' => setting('efactura_client_secret'),
            'efactura_environment' => setting('efactura_environment', 'sandbox'),
        ];
    }
}
