<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .company-info {
            width: 33%;
        }
        .invoice-info {
            width: 33%;
            text-align: center;
        }
        .client-info {
            width: 33%;
        }
        .company-info h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #1e40af;
        }
        .company-info p {
            margin: 5px 0;
        }
        .invoice-info h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #1e40af;
        }
        .invoice-info p {
            margin: 5px 0;
        }
        .client-info p {
            margin: 5px 0;
        }
        .client-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1e40af;
        }
        .company-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1e40af;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1e40af;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .client-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-section p {
            margin: 5px 0;
            font-size: 14px;
        }
        .total-section .total {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header - Three Columns: Seller, Invoice Info, Buyer -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="company-info">
                        <h3>Vanzator</h3>
                        <p><strong>{{ $company['company_name'] ?? 'Nume Firma' }}</strong></p>
                        @if($company['company_reg_number'])
                        <p><strong>CUI:</strong> {{ $company['company_reg_number'] }}</p>
                        @endif
                        @if($company['company_trade_number'])
                        <p><strong>Nr. Reg. Comert:</strong> {{ $company['company_trade_number'] }}</p>
                        @endif
                        @if($company['company_address'])
                        <p><strong>Adresa:</strong> {{ $company['company_address'] }}</p>
                        @endif
                        @if($company['company_iban'])
                        <p><strong>IBAN:</strong> {{ $company['company_iban'] }}</p>
                        @endif
                        @if($company['company_bank'])
                        <p><strong>Banca:</strong> {{ $company['company_bank'] }}</p>
                        @endif
                        @if($company['company_email'])
                        <p><strong>Email:</strong> {{ $company['company_email'] }}</p>
                        @endif
                        @if($company['company_phone'])
                        <p><strong>Telefon:</strong> {{ $company['company_phone'] }}</p>
                        @endif
                    </td>
                    <td class="invoice-info">
                        <h2>FACTURA</h2>
                        <p><strong>Nr. Factura:</strong> {{ $invoice_number }}</p>
                        <p><strong>Data Factura:</strong> {{ $invoice_date }}</p>
                        <p><strong>Scadenta:</strong> {{ $due_date }}</p>
                        <p><strong>Nr. Comanda:</strong> {{ $order_number }}</p>
                    </td>
                    <td class="client-info">
                        <h3>Cumparator</h3>
                        @if($company_data && !empty($company_data))
                        <p><strong>{{ $company_data['name'] }}</strong></p>
                        @if($company_data['cui'])
                        <p><strong>CUI:</strong> {{ $company_data['cui'] }}</p>
                        @endif
                        @if($company_data['reg'])
                        <p><strong>Nr. Reg. Comert:</strong> {{ $company_data['reg'] }}</p>
                        @endif
                        @if($company_data['address'])
                        <p><strong>Adresa:</strong> {{ $company_data['address'] }}</p>
                        @endif
                        @if($company_data['iban'])
                        <p><strong>IBAN:</strong> {{ $company_data['iban'] }}</p>
                        @endif
                        @else
                        <p><strong>{{ $client['name'] }}</strong></p>
                        <p><strong>Email:</strong> {{ $client['email'] }}</p>
                        @if($client['phone'])
                        <p><strong>Telefon:</strong> {{ $client['phone'] }}</p>
                        @endif
                        @if($client['address'])
                        <p><strong>Adresa:</strong> {{ $client['address'] }}</p>
                        @endif
                        @if($client['city'])
                        <p><strong>Oras:</strong> {{ $client['city'] }}</p>
                        @endif
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Products Table -->
        <div class="section">
            <h3>Produse Comandate</h3>
            <table>
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Produs</th>
                        <th width="80">Cantitate</th>
                        <th width="100">Pret fara TVA</th>
                        <th width="60">TVA</th>
                        <th width="100">TVA Valoare</th>
                        <th width="120">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['product']->name ?? 'Produs sters' }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price_without_vat'], 2) }} RON</td>
                        <td>{{ $item['vat_rate'] }}%</td>
                        <td>{{ number_format($item['vat_value'], 2) }} RON</td>
                        <td>{{ number_format($item['line_total'], 2) }} RON</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="total-section">
            <p><strong>Subtotal (fara TVA):</strong> {{ number_format($subtotal ?? 0, 2) }} RON</p>
            <p><strong>TVA Total:</strong> {{ number_format($tva_amount ?? 0, 2) }} RON</p>
            <p class="total"><strong>Total (cu TVA):</strong> {{ number_format($total, 2) }} RON</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            @if($company['invoice_footer_text'])
            <p>{{ $company['invoice_footer_text'] }}</p>
            @endif
            <p>Factura generata automat in data de {{ $invoice_date }}</p>
        </div>
    </div>
</body>
</html>
