<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NIR-{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }
        .company-info, .supplier-info {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            padding-right: 40px;
        }
        .company-info h3, .supplier-info h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .info-row {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        .receipt-details {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
        }
        .receipt-details .info-row {
            display: inline-block;
            margin-right: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        td.text-right {
            text-align: right;
        }
        td.text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            padding: 5px 0;
        }
        .totals-row.total {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-box .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php
    function removeDiacritics($text) {
        $diacritics = [
            'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't',
            'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ț' => 'T'
        ];
        return strtr($text, $diacritics);
    }
    ?>
    <div class="container">
        <div class="header">
            <h1>{{ removeDiacritics('NOTĂ DE INTRARE RECEPȚIE (NIR)') }}</h1>
            <p>{{ removeDiacritics('Nr. NIR-') }}{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <div class="company-info">
                        <h3>{{ removeDiacritics('DATE FIRMĂ') }}</h3>
                        <div class="info-row">
                            <span class="info-label">{{ removeDiacritics('Nume:') }}</span>
                            <span>{{ removeDiacritics($company['name']) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">CUI:</span>
                            <span>{{ $company['cui'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ removeDiacritics('Reg. Com.:') }}</span>
                            <span>{{ removeDiacritics($company['reg_com']) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ removeDiacritics('Adresa:') }}</span>
                            <span>{{ removeDiacritics($company['address']) }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="supplier-info">
                        <h3>{{ removeDiacritics('FURNIZOR') }}</h3>
                        <div class="info-row">
                            <span class="info-label">{{ removeDiacritics('Nume:') }}</span>
                            <span>{{ removeDiacritics($receipt->supplier->name ?? '-') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">CUI:</span>
                            <span>{{ $receipt->supplier->cui ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ removeDiacritics('Reg. Com.:') }}</span>
                            <span>{{ removeDiacritics($receipt->supplier->reg_com ?? '-') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ removeDiacritics('Adresa:') }}</span>
                            <span>{{ removeDiacritics($receipt->supplier->address ?? '-') }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="receipt-details">
            <div class="info-row">
                <span class="info-label">{{ removeDiacritics('Nr. Factura:') }}</span>
                <span>{{ $receipt->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">{{ removeDiacritics('Data Facturii:') }}</span>
                <span>{{ $receipt->invoice_date->format('d.m.Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">{{ removeDiacritics('Data Receptiei:') }}</span>
                <span>{{ $receipt->receipt_date->format('d.m.Y') }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="50">{{ removeDiacritics('Nr. Crt.') }}</th>
                    <th>{{ removeDiacritics('Denumire Produs') }}</th>
                    <th width="60">{{ removeDiacritics('Cantitate') }}</th>
                    <th width="80">{{ removeDiacritics('Pret fara TVA') }}</th>
                    <th width="50">TVA %</th>
                    <th width="80">{{ removeDiacritics('Valoare TVA') }}</th>
                    <th width="80">{{ removeDiacritics('Pret cu TVA') }}</th>
                    <th width="100">{{ removeDiacritics('Total Linie') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipt->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ removeDiacritics($item->product->name) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->purchase_price_without_vat, 2) }} RON</td>
                    <td class="text-center">{{ $item->vat_rate }}%</td>
                    <td class="text-right">{{ number_format($item->vat_value, 2) }} RON</td>
                    <td class="text-right">{{ number_format($item->purchase_price_with_vat, 2) }} RON</td>
                    <td class="text-right">{{ number_format($item->line_total_with_vat, 2) }} RON</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>{{ removeDiacritics('Total fara TVA:') }}</span>
                <span>{{ number_format($receipt->total_without_vat, 2) }} RON</span>
            </div>
            <div class="totals-row">
                <span>{{ removeDiacritics('Total TVA:') }}</span>
                <span>{{ number_format($receipt->total_vat, 2) }} RON</span>
            </div>
            <div class="totals-row total">
                <span>{{ removeDiacritics('TOTAL CU TVA:') }}</span>
                <span>{{ number_format($receipt->total_with_vat, 2) }} RON</span>
            </div>
        </div>

        @if($receipt->notes)
        <div style="margin-top: 20px;">
            <p><strong>{{ removeDiacritics('Note:') }}</strong> {{ removeDiacritics($receipt->notes) }}</p>
        </div>
        @endif

        <div class="signatures">
            <div class="signature-box">
                <p>{{ removeDiacritics('Gestionar') }}</p>
                <div class="signature-line">{{ removeDiacritics('Semnatura') }}</div>
            </div>
            <div class="signature-box">
                <p>{{ removeDiacritics('Contabil') }}</p>
                <div class="signature-line">{{ removeDiacritics('Semnatura') }}</div>
            </div>
            <div class="signature-box">
                <p>{{ removeDiacritics('Administrator') }}</p>
                <div class="signature-line">{{ removeDiacritics('Semnatura') }}</div>
            </div>
        </div>

        <div class="footer">
            <p>{{ removeDiacritics('Document generat automat in data de') }} {{ $receipt->created_at->format('d.m.Y H:i') }}</p>
            <p>Conform OMFP 2634/2015</p>
        </div>
    </div>
</body>
</html>
