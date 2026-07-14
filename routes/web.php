<?php

use App\Livewire\CatalogView;
use App\Livewire\CategoryView;
use App\Livewire\CustomersView;
use App\Livewire\InventoryView;
use App\Livewire\ProductForm;
use App\Livewire\ProductView;
use App\Livewire\ReportView;
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

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/dashboard')->middleware('auth')->group(function(){
    Route::can('isSeller')->get('/catalog', CatalogView::class)->name('admin.catalog');

    Route::can('isPrivilegied')->get('/products',ProductView::class)->name('admin.products');
    Route::can('isPrivilegied')->get('/product/{id}',ProductForm::class)->name('admin.product.id');

    Route::can('isPrivilegied')->get('/categories',CategoryView::class)->name('admin.categories');
    Route::can('isPrivilegied')->get('/kardex',InventoryView::class)->name('admin.kardex');
    Route::can('isAdmin')->get('/stores',StoreView::class)->name('admin.stores');
    Route::can('isAdmin')->get('/store/{store}',StoreForm::class)->name('admin.store.id');

    Route::get('/',SellView::class)->name('admin.sell');

    Route::can('isAdmin')->get('/users',UsersView::class)->name('admin.users');
    Route::get('/customers',CustomersView::class)->name('admin.customers');
    Route::can('isPrivilegied')->get('/transfers',TransfersView::class)->name('admin.transfers');
    Route::get('/sales',SaleView::class)->name('admin.sales');
    Route::can('isAdmin')->get('/reports',ReportView::class)->name('admin.reports');

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

Route::domain('vendex.3306web.site')->group(function(){
    Route::get('/',function (){
        return response()->json(['message' => 'hola a vendex']);
    });
});
