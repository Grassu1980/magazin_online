<?php

namespace App\Services;

use App\Helpers\InvoiceHelper;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

/**
 * Serviciu pentru integrarea cu eFactura ANAF
 */
class EFacturaService
{
    /**
     * Obține URL-ul API în funcție de mediu
     * 
     * @param string $environment
     * @return string
     */
    private function getApiUrl($environment)
    {
        if ($environment === 'live') {
            return 'https://api.anaf.ro/prod/FCTEL/rest';
        }
        
        return 'https://api.anaf.ro/test/FCTEL/rest';
    }

    /**
     * Generează XML conform standardului ANAF (UBL 2.1 + RO eFactura)
     * 
     * @param Invoice $invoice
     * @return string
     */
    public function generateXml(Invoice $invoice)
    {
        $settings = InvoiceHelper::getCompanySettings();
        $invoice->load('order.items.product', 'order.user');

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        // Create root element with namespaces in the same order as the valid XML example
        $invoiceElement = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2', 'Invoice');
        $dom->appendChild($invoiceElement);

        $invoiceElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $invoiceElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:qdt', 'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDataTypes-2');
        $invoiceElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ccts', 'urn:un:unece:uncefact:documentation:2');
        $invoiceElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $invoiceElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:udt', 'urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2');
        $invoiceElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $invoiceElement->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 ../../UBL-2.1(1)/xsd/maindoc/UBL-Invoice-2.1.xsd');

        // Customization ID
        $customizationId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CustomizationID', 'urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1');
        $invoiceElement->appendChild($customizationId);

        // ID Factură
        $id = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', $invoice->invoice_number);
        $invoiceElement->appendChild($id);

        // Data emiterii
        $issueDate = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:IssueDate', $invoice->created_at->format('Y-m-d'));
        $invoiceElement->appendChild($issueDate);

        // Data scadenței
        $dueDate = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:DueDate', $invoice->created_at->format('Y-m-d'));
        $invoiceElement->appendChild($dueDate);

        // Tip factură
        $invoiceTypeCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:InvoiceTypeCode', '380');
        $invoiceElement->appendChild($invoiceTypeCode);

        // Monedă
        $documentCurrencyCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:DocumentCurrencyCode', 'RON');
        $invoiceElement->appendChild($documentCurrencyCode);

        // Furnizor (Seller Party) conform structurii XML valid
        $sellerParty = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:AccountingSupplierParty');
        $invoiceElement->appendChild($sellerParty);

        $party = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Party');
        $sellerParty->appendChild($party);

        // PartyIdentification
        $partyIdentification = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyIdentification');
        $party->appendChild($partyIdentification);

        $partyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', $settings['company_reg_number']);
        $partyIdentification->appendChild($partyId);

        // PartyName
        $partyName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyName');
        $party->appendChild($partyName);

        $nameElement = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $settings['company_name']);
        $partyName->appendChild($nameElement);

        // PostalAddress
        $postalAddress = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PostalAddress');
        $party->appendChild($postalAddress);

        $streetName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:StreetName', $settings['company_address']);
        $postalAddress->appendChild($streetName);

        $cityName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CityName', 'Cluj-Napoca');
        $postalAddress->appendChild($cityName);

        $countrySubentity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CountrySubentity', 'RO-CJ');
        $postalAddress->appendChild($countrySubentity);

        $country = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Country');
        $postalAddress->appendChild($country);

        $identificationCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:IdentificationCode', 'RO');
        $country->appendChild($identificationCode);

        // PartyTaxScheme - după PostalAddress, înainte de PartyLegalEntity
        $partyTaxScheme = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyTaxScheme');
        $party->appendChild($partyTaxScheme);

        $partyTaxSchemeCompanyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CompanyID', 'RO' . $settings['company_reg_number']);
        $partyTaxScheme->appendChild($partyTaxSchemeCompanyId);

        $partyTaxSchemeNode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxScheme');
        $partyTaxScheme->appendChild($partyTaxSchemeNode);

        $partyTaxSchemeId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', 'VAT');
        $partyTaxSchemeNode->appendChild($partyTaxSchemeId);

        // PartyLegalEntity
        $partyLegalEntity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyLegalEntity');
        $party->appendChild($partyLegalEntity);

        $registrationName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:RegistrationName', $settings['company_name']);
        $partyLegalEntity->appendChild($registrationName);

        $companyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CompanyID', $settings['company_reg_number']);
        $partyLegalEntity->appendChild($companyId);

        // Contact
        $contact = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Contact');
        $party->appendChild($contact);

        $contactName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $settings['company_name']);
        $contact->appendChild($contactName);

        // Cumpărător (Buyer Party) conform structurii XML valid
        $buyerParty = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:AccountingCustomerParty');
        $invoiceElement->appendChild($buyerParty);

        $buyerPartyNode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Party');
        $buyerParty->appendChild($buyerPartyNode);

        // Dacă factura este pentru firmă, folosește datele firmei
        if ($invoice->invoice_type === 'company' && $invoice->company_name) {
            // PartyIdentification
            $buyerPartyIdentification = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyIdentification');
            $buyerPartyNode->appendChild($buyerPartyIdentification);

            $buyerPartyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', $invoice->company_cui ?? '');
            $buyerPartyIdentification->appendChild($buyerPartyId);

            // PartyName
            $buyerPartyName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyName');
            $buyerPartyNode->appendChild($buyerPartyName);

            $buyerNameElement = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $invoice->company_name);
            $buyerPartyName->appendChild($buyerNameElement);

            // PostalAddress
            $buyerPostalAddress = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PostalAddress');
            $buyerPartyNode->appendChild($buyerPostalAddress);

            $buyerStreetName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:StreetName', $invoice->company_address ?? $invoice->order->shipping_address);
            $buyerPostalAddress->appendChild($buyerStreetName);

            $buyerCityName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CityName', 'Floresti');
            $buyerPostalAddress->appendChild($buyerCityName);

            $buyerCountrySubentity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CountrySubentity', 'RO-CJ');
            $buyerPostalAddress->appendChild($buyerCountrySubentity);

            $buyerCountry = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Country');
            $buyerPostalAddress->appendChild($buyerCountry);

            $buyerIdentificationCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:IdentificationCode', 'RO');
            $buyerCountry->appendChild($buyerIdentificationCode);

            // PartyTaxScheme
            $buyerPartyTaxScheme = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyTaxScheme');
            $buyerPartyNode->appendChild($buyerPartyTaxScheme);

            $buyerPartyTaxSchemeCompanyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CompanyID', 'RO' . ($invoice->company_cui ?? ''));
            $buyerPartyTaxScheme->appendChild($buyerPartyTaxSchemeCompanyId);

            $buyerPartyTaxSchemeNode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxScheme');
            $buyerPartyTaxScheme->appendChild($buyerPartyTaxSchemeNode);

            $buyerPartyTaxSchemeId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', 'VAT');
            $buyerPartyTaxSchemeNode->appendChild($buyerPartyTaxSchemeId);

            // PartyLegalEntity
            $buyerPartyLegalEntity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyLegalEntity');
            $buyerPartyNode->appendChild($buyerPartyLegalEntity);

            $buyerRegistrationName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:RegistrationName', $invoice->company_name);
            $buyerPartyLegalEntity->appendChild($buyerRegistrationName);

            $buyerCompanyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CompanyID', $invoice->company_cui ?? '');
            $buyerPartyLegalEntity->appendChild($buyerCompanyId);

            // Contact
            $buyerContact = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Contact');
            $buyerPartyNode->appendChild($buyerContact);

            $buyerContactName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $invoice->company_name);
            $buyerContact->appendChild($buyerContactName);
        } else {
            // PartyIdentification pentru persoană fizică
            $buyerPartyIdentification = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyIdentification');
            $buyerPartyNode->appendChild($buyerPartyIdentification);

            $buyerPartyId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', $invoice->client_name);
            $buyerPartyIdentification->appendChild($buyerPartyId);

            // PartyName
            $buyerPartyName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyName');
            $buyerPartyNode->appendChild($buyerPartyName);

            $buyerNameElement = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $invoice->client_name);
            $buyerPartyName->appendChild($buyerNameElement);

            // PostalAddress
            $buyerPostalAddress = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PostalAddress');
            $buyerPartyNode->appendChild($buyerPostalAddress);

            $buyerStreetName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:StreetName', $invoice->order->shipping_address);
            $buyerPostalAddress->appendChild($buyerStreetName);

            $buyerCityName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CityName', 'Floresti');
            $buyerPostalAddress->appendChild($buyerCityName);

            $buyerCountrySubentity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:CountrySubentity', 'RO-CJ');
            $buyerPostalAddress->appendChild($buyerCountrySubentity);

            $buyerCountry = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Country');
            $buyerPostalAddress->appendChild($buyerCountry);

            $buyerIdentificationCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:IdentificationCode', 'RO');
            $buyerCountry->appendChild($buyerIdentificationCode);

            // PartyLegalEntity pentru persoană fizică (fără PartyTaxScheme)
            $buyerPartyLegalEntity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PartyLegalEntity');
            $buyerPartyNode->appendChild($buyerPartyLegalEntity);

            $buyerRegistrationName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:RegistrationName', $invoice->client_name);
            $buyerPartyLegalEntity->appendChild($buyerRegistrationName);

            // Contact
            $buyerContact = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Contact');
            $buyerPartyNode->appendChild($buyerContact);

            $buyerContactName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $invoice->client_name);
            $buyerContact->appendChild($buyerContactName);
        }

        // PaymentMeans
        $paymentMeans = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PaymentMeans');
        $invoiceElement->appendChild($paymentMeans);

        $paymentMeansCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:PaymentMeansCode', '1');
        $paymentMeans->appendChild($paymentMeansCode);

        $payeeFinancialAccount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:PayeeFinancialAccount');
        $paymentMeans->appendChild($payeeFinancialAccount);

        $accountId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', $settings['company_iban'] ?? '');
        $payeeFinancialAccount->appendChild($accountId);

        // TaxTotal - calculate per VAT rate
        $taxTotal = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxTotal');
        $invoiceElement->appendChild($taxTotal);

        $totalTaxAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:TaxAmount', number_format($invoice->tva_amount, 2, '.', ''));
        $totalTaxAmount->setAttribute('currencyID', 'RON');
        $taxTotal->appendChild($totalTaxAmount);

        // Group by VAT rate
        $taxSubtotals = [];
        foreach ($invoice->order->items as $item) {
            $product = $item->product;
            $vatRate = $product->vat_rate ?? 21;
            $priceWithoutVat = $product->price_without_vat ?? ($item->price / (1 + $vatRate / 100));
            $taxableAmount = $priceWithoutVat * $item->quantity;
            $taxAmount = $taxableAmount * ($vatRate / 100);

            if (!isset($taxSubtotals[$vatRate])) {
                $taxSubtotals[$vatRate] = [
                    'taxable_amount' => 0,
                    'tax_amount' => 0,
                ];
            }
            $taxSubtotals[$vatRate]['taxable_amount'] += $taxableAmount;
            $taxSubtotals[$vatRate]['tax_amount'] += $taxAmount;
        }

        // Create TaxSubtotal for each VAT rate
        foreach ($taxSubtotals as $vatRate => $taxData) {
            $taxSubtotal = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxSubtotal');
            $taxTotal->appendChild($taxSubtotal);

            $taxableAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:TaxableAmount', number_format($taxData['taxable_amount'], 2, '.', ''));
            $taxableAmount->setAttribute('currencyID', 'RON');
            $taxSubtotal->appendChild($taxableAmount);

            $taxAmount3 = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:TaxAmount', number_format($taxData['tax_amount'], 2, '.', ''));
            $taxAmount3->setAttribute('currencyID', 'RON');
            $taxSubtotal->appendChild($taxAmount3);

            $taxCategory = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxCategory');
            $taxSubtotal->appendChild($taxCategory);

            $taxCategoryId2 = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', 'S');
            $taxCategory->appendChild($taxCategoryId2);

            $taxPercent2 = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Percent', $vatRate);
            $taxCategory->appendChild($taxPercent2);

            $taxScheme2 = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxScheme');
            $taxCategory->appendChild($taxScheme2);

            $taxSchemeId2 = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', 'VAT');
            $taxScheme2->appendChild($taxSchemeId2);
        }

        // LegalMonetaryTotal conform structurii XML valid
        $legalMonetaryTotal = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:LegalMonetaryTotal');
        $invoiceElement->appendChild($legalMonetaryTotal);

        $lineExtensionAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:LineExtensionAmount', number_format($invoice->subtotal, 2, '.', ''));
        $lineExtensionAmount->setAttribute('currencyID', 'RON');
        $legalMonetaryTotal->appendChild($lineExtensionAmount);

        $taxExclusiveAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:TaxExclusiveAmount', number_format($invoice->subtotal, 2, '.', ''));
        $taxExclusiveAmount->setAttribute('currencyID', 'RON');
        $legalMonetaryTotal->appendChild($taxExclusiveAmount);

        $taxInclusiveAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:TaxInclusiveAmount', number_format($invoice->total, 2, '.', ''));
        $taxInclusiveAmount->setAttribute('currencyID', 'RON');
        $legalMonetaryTotal->appendChild($taxInclusiveAmount);

        $payableAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:PayableAmount', number_format($invoice->total, 2, '.', ''));
        $payableAmount->setAttribute('currencyID', 'RON');
        $legalMonetaryTotal->appendChild($payableAmount);

        // Linii factură (Invoice Lines) conform structurii XML valid
        foreach ($invoice->order->items as $index => $item) {
            $line = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:InvoiceLine');
            $invoiceElement->appendChild($line);

            $lineId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', $index + 1);
            $line->appendChild($lineId);

            $invoicedQuantity = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:InvoicedQuantity', $item->quantity);
            $invoicedQuantity->setAttribute('unitCode', 'EA');
            $line->appendChild($invoicedQuantity);

            $product = $item->product;
            $vatRate = $product->vat_rate ?? 21;
            $priceWithoutVat = $product->price_without_vat ?? ($item->price / (1 + $vatRate / 100));
            $lineExtensionAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:LineExtensionAmount', number_format($priceWithoutVat * $item->quantity, 2, '.', ''));
            $lineExtensionAmount->setAttribute('currencyID', 'RON');
            $line->appendChild($lineExtensionAmount);

            $itemNode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Item');
            $line->appendChild($itemNode);

            $itemName = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Name', $item->product->name);
            $itemNode->appendChild($itemName);

            // CommodityClassification
            $commodityClassification = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:CommodityClassification');
            $itemNode->appendChild($commodityClassification);

            $itemClassificationCode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ItemClassificationCode', $item->product->id);
            $itemClassificationCode->setAttribute('listID', 'EN');
            $commodityClassification->appendChild($itemClassificationCode);

            // ClassifiedTaxCategory
            $classifiedTaxCategory = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:ClassifiedTaxCategory');
            $itemNode->appendChild($classifiedTaxCategory);

            $classifiedTaxCategoryId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', 'S');
            $classifiedTaxCategory->appendChild($classifiedTaxCategoryId);

            $classifiedTaxPercent = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:Percent', $vatRate);
            $classifiedTaxCategory->appendChild($classifiedTaxPercent);

            $classifiedTaxScheme = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:TaxScheme');
            $classifiedTaxCategory->appendChild($classifiedTaxScheme);

            $classifiedTaxSchemeId = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:ID', 'VAT');
            $classifiedTaxScheme->appendChild($classifiedTaxSchemeId);

            $priceNode = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', 'cac:Price');
            $line->appendChild($priceNode);

            $priceAmount = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', 'cbc:PriceAmount', number_format($priceWithoutVat, 2, '.', ''));
            $priceAmount->setAttribute('currencyID', 'RON');
            $priceNode->appendChild($priceAmount);
        }

        return $dom->saveXML();
    }

    /**
     * Trimite factura către ANAF
     * 
     * @param Invoice $invoice
     * @return array
     */
    public function sendToAnaf(Invoice $invoice)
    {
        $settings = InvoiceHelper::getCompanySettings();
        
        if (!$settings['efactura_api_key'] || !$settings['efactura_client_id'] || !$settings['efactura_client_secret']) {
            return [
                'success' => false,
                'message' => 'Setările eFactura nu sunt configurate',
            ];
        }
        
        $xml = $this->generateXml($invoice);
        
        // Salvează XML-ul
        $xmlPath = 'invoices/xml/' . $invoice->invoice_number . '.xml';
        Storage::disk('public')->put($xmlPath, $xml);
        
        // Trimite către ANAF
        $apiUrl = $this->getApiUrl($settings['efactura_environment']);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $settings['efactura_api_key'],
                'Content-Type' => 'application/xml',
            ])->post($apiUrl . '/upload', [
                'xml' => $xml,
            ]);
            
            if ($response->successful()) {
                // Actualizează factura cu statusul
                $invoice->update([
                    'xml_path' => $xmlPath,
                    'efactura_status' => 'sent',
                    'efactura_message' => 'Factura trimisă cu succes',
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Factura trimisă cu succes către ANAF',
                    'response' => $response->json(),
                ];
            } else {
                $invoice->update([
                    'xml_path' => $xmlPath,
                    'efactura_status' => 'error',
                    'efactura_message' => $response->body(),
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Eroare la trimiterea facturii: ' . $response->body(),
                ];
            }
        } catch (\Exception $e) {
            $invoice->update([
                'xml_path' => $xmlPath,
                'efactura_status' => 'error',
                'efactura_message' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Eroare la trimiterea facturii: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifică statusul unei facturi la ANAF
     * 
     * @param Invoice $invoice
     * @return array
     */
    public function checkStatus(Invoice $invoice)
    {
        $settings = InvoiceHelper::getCompanySettings();
        
        if (!$settings['efactura_api_key']) {
            return [
                'success' => false,
                'message' => 'Setările eFactura nu sunt configurate',
            ];
        }
        
        $apiUrl = $this->getApiUrl($settings['efactura_environment']);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $settings['efactura_api_key'],
            ])->get($apiUrl . '/status/' . $invoice->invoice_number);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Eroare la verificarea statusului',
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Eroare la verificarea statusului: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Descarcă XML-ul unei facturi
     * 
     * @param Invoice $invoice
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadXml(Invoice $invoice)
    {
        if (!$invoice->xml_path) {
            abort(404, 'XML-ul nu a fost generat');
        }
        
        $filePath = storage_path('app/public/' . $invoice->xml_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'XML-ul nu a fost găsit');
        }
        
        return response()->download($filePath, $invoice->invoice_number . '.xml');
    }
}
