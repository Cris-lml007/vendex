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
    Route::get('/products',ProductView::class)->name('admin.products');
    Route::get('/categories',CategoryView::class)->name('admin.categories');
    Route::get('/kardex',InventoryView::class)->name('admin.kardex');
    Route::get('/stores',StoreView::class)->name('admin.stores');
});
