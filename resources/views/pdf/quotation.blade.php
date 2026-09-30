<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Proforma #{{ $quotation->id }}</title>

    <style>
        @page {
            margin: 35px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            width: 100%;
            margin-bottom: 25px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
        }

        .title {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
        }

        .quotation-number {
            text-align: right;
            margin-top: 5px;
            color: #555;
        }

        .dates {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .dates td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        .label {
            font-weight: bold;
            background: #f5f5f5;
        }

        .products {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .products th {
            background: #f1f1f1;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .products td {
            border: 1px solid #ddd;
            padding: 7px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .total-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 7px;
        }

        .total-label {
            text-align: right;
            font-weight: bold;
        }

        .total {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #777;
        }

        .validity {
            margin-top: 25px;
            padding: 10px;
            border: 1px solid #ddd;
            background: #fafafa;
        }
    </style>
</head>

<body>
    @php
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists('stores/' . $quotation->user->store_id . '.jpg')) {
            $url = base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get('stores/' . $quotation->user->store_id . '.jpg'));
        } else {
            $url = '';
        }
    @endphp

    {{-- ========================================================= --}}
    {{-- CABECERA --}}
    {{-- ========================================================= --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td width="60%">

                    <div class="company">
                        <!-- Logo -->
                        <div style="width:30%; text-align:left; vertical-align:top;">
                            @if ($url != '')
                                <img src="data:image/png;base64,{{ $url }}" style="height:60px;">
                            @endif
                        </div>
                    </div>

                    @if ($settings)
                        @if ($settings->name ?? false)
                            <div>
                                {{ $settings->name }}
                            </div>
                        @endif
                    @endif

                </td>

                <td width="40%">

                    <div class="title">
                        PROFORMA
                    </div>

                    <div class="quotation-number">
                        N.º {{ str_pad($quotation->id,8,'0',STR_PAD_LEFT) }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- FECHAS --}}
    {{-- ========================================================= --}}

    <table class="dates">

        <tr>

            <td class="label" width="25%">
                Fecha de emisión
            </td>

            <td width="25%">
                {{ $quotation->created_at->format('d/m/Y') }}
            </td>

            <td class="label" width="25%">
                Válida hasta
            </td>

            <td width="25%">
                {{ \Carbon\Carbon::parse($quotation->valid_to)->format('d/m/Y') }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Elaborado por
            </td>

            <td colspan="3">
                {{ $quotation->user->name ?? '-' }}
            </td>

        </tr>

    </table>


    {{-- ========================================================= --}}
    {{-- PRODUCTOS --}}
    {{-- ========================================================= --}}

    <table class="products">

        <thead>

            <tr>

                <th width="8%" class="center">
                    ID
                </th>

                <th>
                    Producto
                </th>

                <th width="12%" class="center">
                    Cant.
                </th>

                <th width="15%" class="right">
                    Precio
                </th>

                <th width="18%" class="right">
                    Subtotal
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach ($quotation->details as $detail)
                <tr>

                    <td class="center">
                        {{ $detail->product_id }}
                    </td>

                    @php
                        $item = $detail->product;
                    @endphp

                    <td>

                        {{ $item->name }} ({{ $item->model }})
                        ({{ $item->color ?? '' }})
                        <br>

                        <small style="color: #777;">
                            {{ $detail->is_unit ? 'Precio unidad' : 'Precio mayor' }}
                        </small>

                    </td>

                    <td class="center">
                        {{ $detail->quantity }}
                    </td>

                    <td class="right">
                        Bs. {{ number_format($detail->price, 2) }}
                    </td>

                    <td class="right">
                        Bs.
                        {{ number_format($detail->quantity * $detail->price, 2) }}
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>


    {{-- ========================================================= --}}
    {{-- TOTAL --}}
    {{-- ========================================================= --}}

    <table class="total-table">

        <tr>

            <td width="70%"></td>

            <td width="15%" class="total-label">
                TOTAL:
            </td>

            <td width="15%" class="total">

                Bs.
                {{ number_format($quotation->details->sum(fn($detail) => $detail->quantity * $detail->price), 2) }}

            </td>

        </tr>

    </table>


    {{-- ========================================================= --}}
    {{-- VIGENCIA --}}
    {{-- ========================================================= --}}

    <div class="validity">

        <strong>
            Vigencia de la proforma:
        </strong>

        Del
        {{ \Carbon\Carbon::parse($quotation->valid_from)->format('d/m/Y') }}

        al

        {{ \Carbon\Carbon::parse($quotation->valid_to)->format('d/m/Y') }}

    </div>


    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <div class="footer">

        Proforma generada por https://vendex.space

    </div>

</body>

</html>
