<?php

use App\Livewire\CategoryView;
use App\Livewire\CustomersView;
use App\Livewire\InventoryView;
use App\Livewire\ProductForm;
use App\Livewire\ProductView;
use App\Livewire\SaleView;
use App\Livewire\SellView;
use App\Livewire\StoreForm;
use App\Livewire\StoreView;
use App\Livewire\TransfersView;
use App\Livewire\UsersView;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/dashboard')->middleware('auth')->group(function(){
    Route::get('/products',ProductView::class)->name('admin.products');
    Route::get('/product/{id}',ProductForm::class)->name('admin.product.id');

    Route::get('/categories',CategoryView::class)->name('admin.categories');
    Route::get('/kardex',InventoryView::class)->name('admin.kardex');
    Route::get('/stores',StoreView::class)->name('admin.stores');
    Route::get('/store/{store}',StoreForm::class)->name('admin.store.id');

    Route::get('/sell',SellView::class)->name('admin.sell');

    Route::get('/users',UsersView::class)->name('admin.users');
    Route::get('/customers',CustomersView::class)->name('admin.customers');
    Route::get('/transfers',TransfersView::class)->name('admin.transfers');
    Route::get('/sales',SaleView::class)->name('admin.sales');

    Route::get('/sell/{transaction}',function (\App\Models\Transaction $transaction){


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
    })->name('admin.sell.id');
});
