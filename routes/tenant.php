<?php

declare(strict_types=1);

use App\Http\Middleware\VerificationStatus;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Livewire\CatalogView;
use App\Livewire\CategoryView;
use App\Livewire\CustomersView;
use App\Livewire\ExchangeView;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    VerificationStatus::class
])->group(function () {

        Route::get('/asset/{type}/{store}.jpg', function ($type,$store) {
            $path = "{$type}/{$store}.jpg";

            abort_unless(
                Storage::disk('local')->exists($path),
                404
            );

            return response()->file(
                Storage::disk('local')->path($path)
            );

        })->name('store.photo');





        Auth::routes([
            'register' => false,
            'reset' => false,
            'verify' => false,
        ]);

        Route::get('/',SellView::class)->middleware('auth');

        // Route::get('/', function () {
        //     return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id'). ' user: '.Auth::user();
        // });

        Route::prefix('/dashboard')->middleware('auth')->group(function(){

            Route::can('isAdmin')->get('/exchange',ExchangeView::class)->name('admin.exchange');

            Route::get('/notifications/get',function (Request $request){
                $stocks = \App\Models\Stock::where('quantity','<=','min_quantity')
                    ->where('min_quantity','!=',0)
                    ->get();

                $notifications = [
                ];

                foreach ($stocks as $stock){
                    $notifications[] = [
                        'icon' => 'fas fa-bell',
                        'text' => 'Stock bajo de '. $stock->product->name. ' en '. $stock->store->name,
                        'url' => route('admin.product.id',$stock->product_id),
                    ];
                }

                // Now, we create the notification dropdown main content.

                $dropdownHtml = '';

                foreach ($notifications as $key => $not) {
                    $icon = "<i class='mr-2 {$not['icon']}'></i>";
                    $url = $not['url'];

                    $dropdownHtml .= "<a href='{$url}' class='dropdown-item'>
                    {$icon}{$not['text']}
                    </a>";

                    if ($key < count($notifications) - 1) {
                        $dropdownHtml .= "<div class='dropdown-divider'></div>";
                    }
                }

                // Return the new notification data.

                return [
                    'label' => count($notifications),
                    'label_color' => 'danger',
                    'icon_color' => 'dark',
                    'dropdown' => $dropdownHtml,
                ];
            })->name('admin.notifications.get');

            Route::can('isAdmin')->get('/attendances', \App\Livewire\AttendancesView::class)->name('admin.attendances');

            Route::get('/profile', \App\Livewire\Profile::class)->name('admin.profile');

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




    });
