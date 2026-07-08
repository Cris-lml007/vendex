<?php

use App\Livewire\CategoryView;
use App\Livewire\InventoryView;
use App\Livewire\ProductForm;
use App\Livewire\ProductView;
use App\Livewire\SellView;
use App\Livewire\StoreForm;
use App\Livewire\StoreView;
use App\Livewire\UsersView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/dashboard')->group(function(){
    Route::get('/products',ProductView::class)->name('admin.products');
    Route::get('/product/{product}',ProductForm::class)->name('admin.product.id');

    Route::get('/categories',CategoryView::class)->name('admin.categories');
    Route::get('/kardex',InventoryView::class)->name('admin.kardex');
    Route::get('/stores',StoreView::class)->name('admin.stores');
    Route::get('/store/{store}',StoreForm::class)->name('admin.store.id');

    Route::get('/sell',SellView::class)->name('admin.sell');

    Route::get('/users',UsersView::class)->name('admin.users');
});
