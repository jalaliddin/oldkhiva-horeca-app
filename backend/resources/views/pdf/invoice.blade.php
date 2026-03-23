<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; background: #fff; }
        .container { padding: 30px 40px; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 30px; border-bottom: 3px solid #1A2744; padding-bottom: 20px; }
        .header-left { display: table-cell; width: 50%; vertical-align: middle; }
        .header-right { display: table-cell; width: 50%; vertical-align: middle; text-align: right; }
        .logo-text { font-size: 28px; font-weight: bold; color: #1A2744; letter-spacing: 2px; }
        .logo-subtitle { font-size: 11px; color: #C8941A; margin-top: 4px; letter-spacing: 1px; text-transform: uppercase; }
        .company-info { font-size: 11px; color: #555; line-height: 1.6; }
        .company-name { font-size: 13px; font-weight: bold; color: #1A2744; }

        /* Invoice title */
        .invoice-title { text-align: center; margin: 25px 0; }
        .invoice-title h1 { font-size: 22px; color: #1A2744; letter-spacing: 3px; }
        .invoice-title .inv-number { font-size: 14px; color: #C8941A; margin-top: 5px; }

        /* Info grid */
        .info-grid { display: table; width: 100%; margin-bottom: 25px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .info-col:last-child { text-align: right; }
        .info-box { background: #f8f9fa; border-left: 4px solid #1A2744; padding: 12px 15px; border-radius: 0 4px 4px 0; display: inline-block; min-width: 200px; }
        .info-box-right { background: #f8f9fa; border-right: 4px solid #C8941A; padding: 12px 15px; border-radius: 4px 0 0 4px; display: inline-block; min-width: 200px; text-align: left; }
        .info-label { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .info-row { font-size: 12px; color: #333; margin-bottom: 3px; }
        .info-row strong { color: #1A2744; }

        /* Booking info */
        .booking-info { background: #1A2744; color: #fff; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; display: table; width: 100%; }
        .booking-info-cell { display: table-cell; text-align: center; }
        .booking-info-label { font-size: 10px; color: #B0BEC5; }
        .booking-info-value { font-size: 13px; font-weight: bold; color: #C8941A; margin-top: 2px; }

        /* Items table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #1A2744; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table th:last-child, .items-table th:nth-child(3), .items-table th:nth-child(4) { text-align: right; }
        .items-table td { padding: 9px 12px; border-bottom: 1px solid #eee; font-size: 12px; }
        .items-table td:last-child, .items-table td:nth-child(3), .items-table td:nth-child(4) { text-align: right; }
        .items-table tr:nth-child(even) td { background: #f9f9f9; }
        .items-table tr:last-child td { border-bottom: none; }

        /* Totals */
        .totals-section { display: table; width: 100%; }
        .totals-left { display: table-cell; width: 55%; vertical-align: bottom; }
        .totals-right { display: table-cell; width: 45%; vertical-align: top; }
        .totals-table { width: 100%; }
        .totals-table td { padding: 6px 10px; font-size: 12px; }
        .totals-table td:last-child { text-align: right; font-weight: bold; }
        .totals-divider td { border-top: 1px solid #ddd; }
        .totals-total td { background: #1A2744; color: #fff; font-size: 14px; border-radius: 4px; }
        .totals-paid td { background: #e8f5e9; color: #2E7D32; font-size: 13px; }
        .totals-balance td { background: #fff3e0; color: #E65100; font-size: 13px; }

        /* Bank info */
        .bank-info { margin-top: 25px; border: 1px solid #ddd; border-radius: 6px; padding: 15px; }
        .bank-info h3 { font-size: 12px; color: #1A2744; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .bank-row { font-size: 11px; color: #555; margin-bottom: 4px; }
        .bank-row strong { color: #333; }

        /* Status badge */
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-unpaid { background: #fff3e0; color: #E65100; }
        .status-partial { background: #e3f2fd; color: #1565C0; }
        .status-paid { background: #e8f5e9; color: #2E7D32; }
        .status-overdue { background: #ffebee; color: #C62828; }

        /* Footer */
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="logo-text">OLD KHIVA</div>
            <div class="logo-subtitle">Restaurant & Bar</div>
        </div>
        <div class="header-right">
            <div class="company-info">
                <div class="company-name">{{ $settings['restaurant_name'] ?? 'OldKhiva Restaurant' }}</div>
                {{ $settings['restaurant_address'] ?? 'Xiva, Ichan-Qala 1' }}<br>
                Tel: {{ $settings['restaurant_phone'] ?? '+998 (62) 375-00-00' }}<br>
                Email: {{ $settings['restaurant_email'] ?? 'info@oldkhiva.uz' }}<br>
                INN: {{ $settings['restaurant_inn'] ?? '' }}
            </div>
        </div>
    </div>

    <!-- Invoice Title -->
    <div class="invoice-title">
        <h1>INVOICE</h1>
        <div class="inv-number">{{ $invoice->invoice_number }}</div>
    </div>

    <!-- Client and Invoice Info -->
    <div class="info-grid">
        <div class="info-col">
            <div class="info-box">
                <div class="info-label">Mijoz ma'lumotlari</div>
                <div class="info-row"><strong>{{ $invoice->client->company_name ?? $invoice->client->name }}</strong></div>
                @if($invoice->client->inn)
                    <div class="info-row">INN: {{ $invoice->client->inn }}</div>
                @endif
                @if($invoice->client->director_name)
                    <div class="info-row">Direktor: {{ $invoice->client->director_name }}</div>
                @endif
                @if($invoice->client->address)
                    <div class="info-row">{{ $invoice->client->address }}</div>
                @endif
                @if($invoice->client->phone)
                    <div class="info-row">Tel: {{ $invoice->client->phone }}</div>
                @endif
            </div>
        </div>
        <div class="info-col">
            <div class="info-box-right">
                <div class="info-label">Invoice ma'lumotlari</div>
                <div class="info-row">Sana: <strong>{{ $invoice->created_at->format('d.m.Y') }}</strong></div>
                @if($invoice->due_date)
                    <div class="info-row">To'lov muddati: <strong>{{ $invoice->due_date->format('d.m.Y') }}</strong></div>
                @endif
                <div class="info-row">Holat:
                    <strong>
                        @switch($invoice->status)
                            @case('paid') To'langan @break
                            @case('partial') Qisman to'langan @break
                            @case('overdue') Muddati o'tgan @break
                            @default To'lanmagan
                        @endswitch
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Info -->
    @if($invoice->booking)
        <div class="booking-info">
            <div class="booking-info-cell">
                <div class="booking-info-label">Bron raqami</div>
                <div class="booking-info-value">{{ $invoice->booking->booking_number }}</div>
            </div>
            <div class="booking-info-cell">
                <div class="booking-info-label">Tadbir sanasi</div>
                <div class="booking-info-value">{{ $invoice->booking->event_date->format('d.m.Y') }}</div>
            </div>
            @if($invoice->booking->event_time)
                <div class="booking-info-cell">
                    <div class="booking-info-label">Vaqt</div>
                    <div class="booking-info-value">{{ $invoice->booking->event_time }}</div>
                </div>
            @endif
            <div class="booking-info-cell">
                <div class="booking-info-label">Mehmonlar soni</div>
                <div class="booking-info-value">{{ $invoice->booking->guest_count }} kishi</div>
            </div>
        </div>
    @endif

    <!-- Items Table -->
    <table class="items-table">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 45%">Taom / Xizmat nomi</th>
            <th style="width: 15%">Narxi</th>
            <th style="width: 10%">Miqdor</th>
            <th style="width: 25%">Jami</th>
        </tr>
        </thead>
        <tbody>
        @if($invoice->booking && $invoice->booking->items->count())
            @foreach($invoice->booking->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ number_format($item->item_price, 0, '.', ' ') }} so'm</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->subtotal, 0, '.', ' ') }} so'm</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <div class="totals-left">
            @if($invoice->booking && $invoice->booking->notes)
                <div style="font-size: 11px; color: #666; background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 10px;">
                    <strong>Izoh:</strong> {{ $invoice->booking->notes }}
                </div>
            @endif
        </div>
        <div class="totals-right">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td>{{ number_format($invoice->subtotal, 0, '.', ' ') }} so'm</td>
                </tr>
                @if($invoice->tax_rate > 0)
                    <tr>
                        <td>Soliq ({{ $invoice->tax_rate }}%):</td>
                        <td>{{ number_format($invoice->tax_amount, 0, '.', ' ') }} so'm</td>
                    </tr>
                @endif
                <tr class="totals-divider">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="totals-total">
                    <td style="padding: 10px;">JAMI:</td>
                    <td style="padding: 10px;">{{ number_format($invoice->total_amount, 0, '.', ' ') }} so'm</td>
                </tr>
                <tr class="totals-paid">
                    <td>To'langan:</td>
                    <td>{{ number_format($invoice->paid_amount, 0, '.', ' ') }} so'm</td>
                </tr>
                <tr class="totals-balance">
                    <td>Qoldiq:</td>
                    <td>{{ number_format($invoice->balance, 0, '.', ' ') }} so'm</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Bank Info -->
    <div class="bank-info">
        <h3>Bank rekvizitlari</h3>
        <div class="bank-row">Bank: <strong>{{ $settings['restaurant_bank'] ?? '' }}</strong></div>
        <div class="bank-row">MFO: <strong>{{ $settings['restaurant_mfo'] ?? '' }}</strong></div>
        <div class="bank-row">Hisob raqam: <strong>{{ $settings['restaurant_account'] ?? '' }}</strong></div>
        <div class="bank-row">INN: <strong>{{ $settings['restaurant_inn'] ?? '' }}</strong></div>
        <div class="bank-row">To'lov maqsadi: <strong>{{ $invoice->invoice_number }} bo'yicha to'lov</strong></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Bu hujjat kompyuter tomonidan yaratilgan va imzo hamda muhr talab etmaydi.</p>
        <p style="margin-top: 5px;">{{ $settings['restaurant_name'] ?? 'OldKhiva Restaurant' }} | {{ $settings['restaurant_website'] ?? 'www.oldkhiva.uz' }} | {{ $settings['restaurant_phone'] ?? '' }}</p>
    </div>
</div>
</body>
</html>
