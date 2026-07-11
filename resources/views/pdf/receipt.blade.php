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

<div class="title">
    {{ $transaction->store->name }}
</div>

<div class="subtitle">
    {{ $transaction->store->address ?? '' }}
</div>

<div class="subtitle">
    Teléfono: {{ $transaction->store->phone ?? '' }} &nbsp;&nbsp;&nbsp; Email: {{ $transaction->store->email ?? '' }}
</div>

<br>

<div class="title" style="font-size:16px">
    RECIBO DE VENTA
</div>

<table class="header section">

    <tr>
        <td width="20%"><strong>N° Venta:</strong></td>
        <td width="30%">{{ $transaction->id }}</td>

        <td width="20%"><strong>Fecha:</strong></td>
        <td width="30%">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
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
        <td><strong>Vendedor:</strong></td>
        <td colspan="3">
            {{ $transaction->user->name }}
        </td>
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
                {{ $detail->product->name }}
            </td>

            <td class="text-right">
                {{ number_format($detail->price,2) }}
            </td>

            <td class="text-right">
                {{ number_format($detail->quantity * $detail->price,2) }}
            </td>

        </tr>

    @endforeach

    <tr>

        <td colspan="3" class="text-right total">
            TOTAL
        </td>

        <td class="text-right total">
            Bs. {{ number_format($transaction->total,2) }}
        </td>

    </tr>

    </tbody>

</table>

<div class="literal">

    <strong>SON:</strong>
    {{ strtoupper($format->format($transaction->total)) }} BOLIVIANOS

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
