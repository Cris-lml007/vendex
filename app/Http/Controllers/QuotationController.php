<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function getPdf(Quotation $quotation) {

        $quotation->load([
            'user',
            'details.product',
        ]);


        $settings = Settings::first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pdf.quotation',
            [
                'quotation' => $quotation,
                'settings' => $settings,
            ]
        );

        return $pdf->stream(
            'proforma-' . $quotation->id . '.pdf'
        );
    }
}
