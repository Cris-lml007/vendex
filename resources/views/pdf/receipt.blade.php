<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Venta</title>

    <style>

        @page{
            margin:25px;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:12px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .title{
            text-align:center;
            font-size:20px;
            font-weight:bold;
        }

        .subtitle{
            text-align:center;
            font-size:11px;
        }

        .section{
            margin-top:18px;
        }

        .border{
            border:1px solid #000;
        }

        .border td,
        .border th{
            border:1px solid #000;
            padding:6px;
        }

        .header td{
            padding:3px;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .bold{
            font-weight:bold;
        }

        .total{
            font-size:14px;
            font-weight:bold;
        }

        .literal{
            margin-top:20px;
            border:1px solid #000;
            padding:10px;
        }

        .signature{
            margin-top:70px;
        }

        .signature td{
            width:50%;
            text-align:center;
        }

        .line{
            width:220px;
            border-top:1px solid #000;
            margin:0 auto;
            padding-top:5px;
        }

    </style>

</head>
<body>


@php
    if(\Illuminate\Support\Facades\Storage::disk('local')->exists("stores/".$transaction->store_id .".jpg"))
        $url = base64_encode(\Illuminate\Support\Facades\Storage::disk('local')
        ->get("stores/".$transaction->store_id .".jpg"));
    else{
        $url = '';
    }
@endphp

<table style="width:100%; border-bottom:1px solid #000; padding-bottom:10px;">
    <tr>

        <!-- Logo -->
        <td style="width:30%; text-align:left; vertical-align:top;">
            @if($url != '')
            <img src="data:image/png;base64,{{ $url }}"
                 style="height:70px;">
            @endif
        </td>
        <!-- Título -->
        <td style="width:34%; text-align:center; vertical-align:middle;">
            <div style="font-size:22px; font-weight:bold;">
                RECIBO DE VENTA
            </div>

            <div style="font-size:11px;">
                N° {{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}
            </div>
        </td>

        <!-- Datos de la tienda -->
        <td style="width:33%; text-align:right; font-size:11px;">

            <strong>{{ $transaction->store->name }}</strong><br>

            {{ $transaction->store->address }}<br>

            Cel: {{ $transaction->store->cellphone }}<br>

            {{ $transaction->store->email }}

        </td>

    </tr>
</table>


<table class="header section">

    <tr>
        <td width="20%"><strong>Fecha:</strong></td>
        <td width="30%">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        <td width="20%"><strong>Vendedor:</strong></td>
        <td width="30%">
            {{ $transaction->user->name }}
        </td>
    </tr>

    <tr>
        <td><strong>Cliente:</strong></td>
        <td>{{ $transaction->customer->name ?? '---' }}</td>

        <td><strong>CI:</strong></td>
        <td>{{ $transaction->customer->ci ?? '---' }}</td>
    </tr>

    <tr>
        <td><strong>Celular:</strong></td>
        <td>{{ $transaction->customer->phone ?? '---'}}</td>

        <td><strong>Email:</strong></td>
        <td>{{ $transaction->customer->email ?? '---'}}</td>
    </tr>
    <tr>
        <td><strong>Metodo de Pago:</strong></td>
        <td>{{ __('messages.'.$transaction->payment_method->name) ?? '' }}</td>
    </tr>

</table>

<table class="border section">

    <thead>

    <tr>

        <th width="8%">Cant.</th>

        <th>
            Producto
        </th>

        <th width="18%">
            Precio
        </th>

        <th width="18%">
            Subtotal
        </th>

    </tr>

    </thead>

    <tbody>

    @foreach($transaction->details as $detail)

        <tr>

            <td class="text-center">
                {{ $detail->quantity }}
            </td>

            <td>
                {{ $detail->product->name }} ({{ $detail->product->id }})
            </td>

            <td class="text-right">
                {{ number_format($detail->price * $detail->exchange_rate->usd_to_bs,2) }}
            </td>

            <td class="text-right">
                {{ number_format($detail->quantity * $detail->price * $detail->exchange_rate->usd_to_bs,2) }}
            </td>

        </tr>

    @endforeach

    <tr>

        <td colspan="3" class="text-right total">
            TOTAL
        </td>

        <td class="text-right total">
            Bs. {{ number_format($transaction->total * $transaction->details[0]->exchange_rate->usd_to_bs,2) }}
        </td>

    </tr>

    </tbody>

</table>

<div class="literal">
    <strong>Observacion: </strong>
    {{ $transaction->observation ?? '' }}
</div>

<div class="literal">
    <strong>SON:</strong>
    {{ strtoupper($format->format($transaction->total * $transaction->details[0]->exchange_rate->usd_to_bs)) }} BOLIVIANOS

</div>

<table class="signature">

    <tr>

        <td>

            <div class="line">
                Firma del Cliente
            </div>

        </td>

        <td>

            <div class="line">
                Firma del Vendedor
            </div>

        </td>

    </tr>

</table>

</body>
</html>
