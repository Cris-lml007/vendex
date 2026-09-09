<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111111;
            margin: 0;
            padding: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | CONTENEDOR
        |--------------------------------------------------------------------------
        |
        | No utilizamos flexbox.
        | El ancho se controla desde el PDF mediante setPaper().
        |
        */

        .receipt {
            width: 95%;
            padding: 8px 8px 15px 8px;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header {
            text-align: center;
        }

        .logo {
            /* width: 65px; */
            height: 70px;
            margin-bottom: 5px;
        }

        .store-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .store-info {
            font-size: 8px;
            line-height: 1.4;
        }


        /*
        |--------------------------------------------------------------------------
        | RECIBO
        |--------------------------------------------------------------------------
        */

        .receipt-title {
            margin-top: 8px;
            padding: 5px 0;

            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;

            text-align: center;
        }

        .receipt-title-main {
            font-size: 12px;
            font-weight: bold;
        }

        .receipt-number {
            font-size: 9px;
            margin-top: 2px;
        }


        /*
        |--------------------------------------------------------------------------
        | INFORMACIÓN
        |--------------------------------------------------------------------------
        */

        .info {
            margin-top: 7px;
            margin-bottom: 7px;
        }

        .info-row {
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .info-label {
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLA
        |--------------------------------------------------------------------------
        */

        .products {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .products thead {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .products th {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 2px;
            vertical-align: middle;
        }

        .products td {
            font-size: 7px;
            padding: 4px 2px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | ANCHOS DE COLUMNAS
        |--------------------------------------------------------------------------
        |
        | Importante:
        | Estos porcentajes suman exactamente 100%.
        |
        */

        .col-product {
            width: 43%;
            text-align: left !important;
        }

        .col-quantity {
            width: 12%;
            text-align: center !important;
        }

        .col-price {
            width: 21%;
            text-align: right !important;
        }

        .col-subtotal {
            width: 24%;
            text-align: right !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCTO
        |--------------------------------------------------------------------------
        */

        .product-id {
            font-weight: bold;
        }

        .product-name {
            font-weight: bold;
        }

        .product-model {
            font-size: 6px;
            margin-top: 1px;
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        .total-container {
            width: 100%;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-top: 5px;
            padding: 6px 0;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .total-label {
            width: 70%;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
        }

        .total-value {
            width: 30%;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | CAJERO
        |--------------------------------------------------------------------------
        */

        .cashier {
            margin-top: 7px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #000;
            font-size: 8px;
        }


        /*
        |--------------------------------------------------------------------------
        | QR
        |--------------------------------------------------------------------------
        */

        .qr {
            text-align: center;
            margin-top: 8px;
        }

        .qr img {
            width: 75px;
            height: 75px;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .footer {
            text-align: center;
            margin-top: 27px;
            font-size: 8px;
            line-height: 1.5;
        }

        .footer-message {
            font-size: 9px;
            font-weight: bold;
        }

        .separator {
            margin: 5px 0;
            font-size: 7px;
        }

    </style>
    <title>Recibo de Venta</title>
</head>


@php

    $logo = '';

    if (
        \Illuminate\Support\Facades\Storage::disk('local')
            ->exists('stores/' . $transaction->store_id . '.jpg')
    ) {
        $logo = base64_encode(
            \Illuminate\Support\Facades\Storage::disk('local')
                ->get('stores/' . $transaction->store_id . '.jpg')
        );
    }

@endphp


<body>

    <div class="receipt">


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="header">

            @if ($logo)

                <img
                    src="data:image/jpeg;base64,{{ $logo }}"
                    class="logo"
                    alt="Logo">

            @endif


            <div class="store-name">
                {{ $transaction->store->name }}
            </div>


            <div class="store-info">

                @if ($transaction->store->address)
                    {{ $transaction->store->address }}<br>
                @endif

                @if ($transaction->store->cellphone)
                    Cel: {{ $transaction->store->cellphone }}<br>
                @endif

                @if ($transaction->store->email)
                    {{ $transaction->store->email }}
                @endif

            </div>

        </div>



        {{-- =====================================================
             RECIBO
        ====================================================== --}}

        <div class="receipt-title">

            <div class="receipt-title-main">
                RECIBO DE VENTA
            </div>

            <div class="receipt-number">
                N° {{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}
            </div>

        </div>



        {{-- =====================================================
             INFORMACIÓN
        ====================================================== --}}

        <div class="info">

            <div class="info-row">
                <span class="info-label">Cliente:</span>
                {{ $transaction?->customer?->name ?? 'Sin nombre' }}
            </div>

            <div class="info-row">
                <span class="info-label">CI/NIT:</span>
                {{ $transaction?->customer?->ci ?? '---' }}
            </div>

            <div class="info-row">
                <span class="info-label">Fecha:</span>
                {{ $transaction->created_at }}
            </div>

            <div class="info-row">
                <span class="info-label">Tipo de Pago:</span>
                {{ __('messages.' . $transaction->payment_method->name) }}
            </div>

        </div>



        {{-- =====================================================
             PRODUCTOS
        ====================================================== --}}

        <table class="products">

            <thead>

                <tr>

                    <th class="col-product">
                        Producto
                    </th>

                    <th class="col-quantity">
                        Cant.
                    </th>

                    <th class="col-price">
                        Precio
                    </th>

                    <th class="col-subtotal">
                        Subtotal
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach ($transaction->details ?? [] as $detail)

                    <tr>

                        {{-- PRODUCTO --}}

                        <td class="col-product">

                            <div class="product-id">
                                {{ $detail->product->id }}
                            </div>

                            <div class="product-name">
                                {{ $detail->product->name }}
                            </div>

                            @if ($detail->product->model)

                                <div class="product-model">
                                    Modelo: {{ $detail->product->model }}
                                </div>

                            @endif

                        </td>


                        {{-- CANTIDAD --}}

                        <td class="col-quantity">
                            {{ $detail->quantity }}
                        </td>


                        {{-- PRECIO --}}

                        <td class="col-price">

                            {{ number_format(
                                $detail->price *
                                $detail->exchange_rate->usd_to_bs,
                                2
                            ) }}

                        </td>


                        {{-- SUBTOTAL --}}

                        <td class="col-subtotal">

                            {{ number_format(
                                $detail->quantity *
                                $detail->price *
                                $detail->exchange_rate->usd_to_bs,
                                2
                            ) }}

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>



        {{-- =====================================================
             TOTAL
        ====================================================== --}}

        <div class="total-container">

            <table class="total-table">

                <tr>

                    <td class="total-label">
                        TOTAL:
                    </td>

                    <td class="total-value">

                        Bs
                        {{ number_format(
                            $transaction->total,
                            2
                        ) }}

                    </td>

                </tr>

            </table>

        </div>



        {{-- =====================================================
             CAJERO
        ====================================================== --}}

        <div class="cashier">

            <strong>Cajero:</strong>

            {{ $transaction?->user?->name ?? '---' }}

        </div>



        {{-- =====================================================
             QR
        ====================================================== --}}

        @if (!empty($qr))

            <div class="qr">

                <img
                    src="data:image/png;base64,{{ base64_encode($qr) }}"
                    alt="Código QR">

            </div>

        @endif



        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <div class="footer">

            <div class="footer-message">
                ¡Gracias por su compra!
            </div>

            <div class="separator">
                --------------------------------
            </div>

            <div>
                Potenciado por <strong>{{ env('DOMAIN_CENTRAL') }}</strong>
            </div>

        </div>


    </div>

</body>

</html>
