<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use NumberFormatter;

class ReceiptController extends Controller
{
    public function getLetter(Transaction $transaction){
        $format = new NumberFormatter('es',NumberFormatter::SPELLOUT);
        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])->loadView('pdf.receipt',[
                'transaction' => $transaction,
                'format' => $format,
            ]);
        $pdf->setPaper('letter', 'landscape');
        $pdf->render();
        return $pdf->stream();
    }

    public function getThermal(Transaction $transaction){
        $format = new NumberFormatter('es',NumberFormatter::SPELLOUT);
        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])->loadView('pdf.receipt-thermal',[
                'transaction' => $transaction,
                'format' => $format,
            ]);
        $width = 80 * 72 / 25.4;
        $height = 400 + ($transaction->details()->count() * 30);

        $pdf->setPaper([0, 0, $width, $height]);
        $pdf->render();
        return $pdf->stream();
    }
}
