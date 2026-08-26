@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    if(\Illuminate\Support\Facades\Storage::disk('local')->exists("stores/".\Illuminate\Support\Facades\Auth::user()->store_id .".jpg")){
        $url = route('store.photo',["stores",\Illuminate\Support\Facades\Auth::user()->store_id]);
    }else{
        $url = asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png'));
    }
@endphp



@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand logo-switch {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link logo-switch {{ config('adminlte.classes_brand') }}"
    @endif>

    {{-- Small brand logo --}}
    <img src="{{ $url  }}"
         alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
         class="{{ config('adminlte.logo_img_class', 'brand-image-xl') }} logo-xs">

    {{-- Large brand logo --}}
    <img src="{{ $url }}"
         alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
         class="{{ config('adminlte.logo_img_xl_class', 'brand-image-xs') }} logo-xl">

</a>
