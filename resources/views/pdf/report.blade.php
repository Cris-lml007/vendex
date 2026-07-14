<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:11px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            border:1px solid #000;
            padding:5px;
        }

        th{
            background:#e5e5e5;
        }

        .title{
            text-align:center;
            font-size:20px;
            font-weight:bold;
        }

        .subtitle{
            text-align:center;
            margin-bottom:20px;
        }

        .resume td{
            border:none;
            padding:3px;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

    </style>

</head>

<body>

<div class="title">

    {{ config('app.name') }}

</div>

<div class="subtitle">

    REPORTE DE VENTAS

</div>

<table class="resume">

    <tr>

        <td><strong>Generado:</strong></td>

        <td>{{ now()->format('d/m/Y H:i') }}</td>

        <td><strong>Desde:</strong></td>

        <td>{{ $from ?: 'Todas' }}</td>

        <td><strong>Hasta:</strong></td>

        <td>{{ $to ?: 'Todas' }}</td>

    </tr>

</table>

<br>

<table>

    <tr>

        <th>Total Ventas</th>

        <th>Ingreso Total</th>

        <th>Productos Vendidos</th>

        <th>Ticket Promedio</th>

    </tr>

    <tr>

        <td class="text-center">

            {{ $totalSales }}

        </td>

        <td class="text-right">

            Bs. {{ number_format($totalAmount,2) }}

        </td>

        <td class="text-center">

            {{ $productsSold }}

        </td>

        <td class="text-right">

            Bs. {{ number_format($averageSale,2) }}

        </td>

    </tr>

</table>

<br>

<table>

    <thead>

    <tr>

        <th>#</th>

        <th>Fecha</th>

        <th>Tienda</th>

        <th>Cliente</th>

        <th>Vendedor</th>

        <th>Productos</th>

        <th>Total</th>

    </tr>

    </thead>

    <tbody>

    @foreach($transactions as $transaction)

        <tr>

            <td>

                {{ $transaction->id }}

            </td>

            <td>

                {{ $transaction->created_at->format('d/m/Y H:i') }}

            </td>

            <td>

                {{ $transaction->store->name }}

            </td>

            <td>

                {{ $transaction?->customer?->name ?? '---'}}

            </td>

            <td>

                {{ $transaction->user->name }}

            </td>

            <td class="text-center">

                {{ $transaction->details->sum('quantity') }}

            </td>

            <td class="text-right">

                Bs.

                {{ number_format(
                    $transaction->details->sum(fn($d)=>$d->quantity*$d->price),
                2) }}

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

</body>
</html>
