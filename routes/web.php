<?php

use App\Livewire\CategoryView;
use App\Livewire\InventoryView;
use App\Livewire\ProductView;
use App\Livewire\StoreView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/dashboard')->group(function(){
    Route::get('/product',ProductView::class)->name('admin.product');
    Route::get('/category',CategoryView::class)->name('admin.category');
    Route::get('/inventory',InventoryView::class)->name('admin.inventory');
    Route::get('/store',StoreView::class)->name('admin.store');
});
