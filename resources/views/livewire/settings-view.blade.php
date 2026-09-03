<x-slot name="header">
    <h1>Configuraciones</h1>
</x-slot>

<div>

    {{-- ========================================================= --}}
    {{-- MENU PRINCIPAL DE CONFIGURACIONES --}}
    {{-- ========================================================= --}}

    @if (!$section)
        <div class="row">

            {{-- Ventas --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-primary h-100" style="cursor: pointer" wire:click="$set('section', 'sales')">

                    <div class="inner">
                        <h4>Ventas</h4>
                        <p>Precios y opciones comerciales</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-cash-register"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>


            {{-- Productos --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-success h-100" style="cursor: pointer"
                    wire:click="$set('section', 'products')">

                    <div class="inner">
                        <h4>Productos</h4>
                        <p>Serialización y gestión</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>


            {{-- Transferencias --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-warning h-100" style="cursor: pointer"
                    wire:click="$set('section', 'transfers')">

                    <div class="inner">
                        <h4>Transferencias</h4>
                        <p>Permisos entre tiendas</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>


            {{-- Usuarios --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-info h-100" style="cursor: pointer" wire:click="$set('section', 'users')">

                    <div class="inner">
                        <h4>Usuarios</h4>
                        <p>Seguridad y acceso</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>


            {{-- Experiencia --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-purple h-100" style="cursor: pointer"
                    wire:click="$set('section', 'experience')">

                    <div class="inner">
                        <h4>Experiencia</h4>
                        <p>Tutorial y comportamiento inicial</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>

            {{-- Apariencia --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-primary h-100" style="cursor:pointer"
                    wire:click="$set('section', 'appearance')">

                    <div class="inner">
                        <h4>Apariencia</h4>
                        <p>Temas y colores del sistema</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-palette"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>

            {{-- Sistema --}}
            <div class="col-lg-4 col-md-6 mb-3">

                <div class="small-box bg-secondary h-100" style="cursor: pointer"
                    wire:click="$set('section', 'system')">

                    <div class="inner">
                        <h4>Sistema</h4>
                        <p>Información y versión</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-cogs"></i>
                    </div>

                    <div class="small-box-footer">
                        Configurar
                        <i class="fas fa-arrow-circle-right"></i>
                    </div>

                </div>

            </div>
        </div>
    @endif



    {{-- ========================================================= --}}
    {{-- CONFIGURACION DE VENTAS --}}
    {{-- ========================================================= --}}

    @if ($section === 'sales')
        <div class="mb-3">

            <button class="btn btn-secondary" wire:click="$set('section', null)">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </button>

        </div>


        <x-card>

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-cash-register mr-2"></i>
                    Configuración de Ventas
                </h3>

            </div>


            <div class="card-body p-0">


                {{-- Precio por Mayor --}}
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

                    <div>

                        <h5 class="mb-1">
                            Precio por Mayor
                        </h5>

                        <small class="text-secondary">
                            Permitir precios especiales para ventas al por mayor.
                        </small>

                    </div>


                    <div class="custom-control custom-switch">

                        <input type="checkbox" class="custom-control-input" id="wholesale_price"
                            wire:model.live="wholesale_price">

                        <label class="custom-control-label" for="wholesale_price"></label>

                    </div>

                </div>


            </div>

        </x-card>
    @endif



    {{-- ========================================================= --}}
    {{-- CONFIGURACION DE PRODUCTOS --}}
    {{-- ========================================================= --}}

@if ($section === 'products')

    <div class="mb-3">

        <button class="btn btn-secondary" wire:click="$set('section', null)">
            <i class="fas fa-arrow-left mr-1"></i>
            Volver
        </button>

    </div>


    <x-card>

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-boxes mr-2"></i>

                Configuración de Productos

            </h3>

        </div>


        <div class="card-body p-0">


            {{-- Productos Serializados --}}
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

                <div>

                    <h5 class="mb-1">
                        Productos Serializados
                    </h5>

                    <small class="text-secondary">
                        Permitir controlar productos mediante número de serie.
                    </small>

                </div>


                <div class="custom-control custom-switch">

                    <input
                        type="checkbox"
                        class="custom-control-input"
                        id="serialized_products"
                        wire:model.live="serialized_products"
                    >

                    <label
                        class="custom-control-label"
                        for="serialized_products">
                    </label>

                </div>

            </div>


            {{-- Productos Heredados --}}
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

                <div>

                    <h5 class="mb-1">
                        Productos Heredados
                    </h5>

                    <small class="text-secondary">
                        Permitir gestionar productos heredados entre tiendas.
                    </small>

                </div>


                <div class="custom-control custom-switch">

                    <input
                        type="checkbox"
                        class="custom-control-input"
                        id="inherited_products"
                        wire:model.live="inherited_products"
                    >

                    <label
                        class="custom-control-label"
                        for="inherited_products">
                    </label>

                </div>

            </div>


            {{-- Etiquetas de Productos --}}
            <div class="p-3 border-bottom">

                <div class="mb-3">

                    <h5 class="mb-1">
                        Etiquetas de Productos
                    </h5>

                    <small class="text-secondary">
                        Selecciona las etiquetas que deseas mostrar como columnas
                        adicionales en el listado de productos.
                    </small>

                </div>


                <div class="row">

                    @forelse($available_tags as $tag)

                        <div class="col-md-4 col-lg-3 mb-2">

                            <div class="custom-control custom-checkbox">

                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="tag_{{ Str::slug($tag) }}"
                                    wire:model="selected_tags"
                                    value="{{ $tag }}"
                                >
                                <label
                                    class="custom-control-label"
                                    for="tag_{{ Str::slug($tag) }}"
                                >
                                    {{ $tag }}
                                </label>

                            </div>

                        </div>

                    @empty

                        <div class="col-12">

                            <small class="text-secondary">
                                No hay etiquetas de productos disponibles.
                            </small>

                        </div>

                    @endforelse

                </div>

            </div>


            {{-- Guardar --}}
            <div class="d-flex justify-content-end p-3">

                <button
                    type="button"
                    class="btn btn-primary"
                    wire:click="save"
                >
                    <i class="fas fa-save mr-1"></i>
                    Guardar configuración
                </button>

            </div>


        </div>

    </x-card>

@endif



    {{-- ========================================================= --}}
    {{-- CONFIGURACION DE TRANSFERENCIAS --}}
    {{-- ========================================================= --}}

    @if ($section === 'transfers')
        <div class="mb-3">

            <button class="btn btn-secondary" wire:click="$set('section', null)">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </button>

        </div>


        <x-card>

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-exchange-alt mr-2"></i>

                    Configuración de Transferencias

                </h3>

            </div>


            <div class="card-body p-0">


                {{-- Transferencias --}}
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>

                        <h5 class="mb-1">
                            Permitir Transferencias a Todos
                        </h5>

                        <small class="text-secondary">
                            Permitir transferencias entre todas las tiendas.
                        </small>

                    </div>


                    <div class="custom-control custom-switch">

                        <input type="checkbox" class="custom-control-input" id="transfers_to_all"
                            wire:model.live="transfers_to_all">

                        <label class="custom-control-label" for="transfers_to_all"></label>

                    </div>

                </div>


            </div>

        </x-card>
    @endif



    {{-- ========================================================= --}}
    {{-- CONFIGURACION DE USUARIOS --}}
    {{-- ========================================================= --}}

    @if ($section === 'users')
        <div class="mb-3">

            <button class="btn btn-secondary" wire:click="$set('section', null)">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </button>

        </div>


        <x-card>

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-users mr-2"></i>

                    Configuración de Usuarios

                </h3>

            </div>


            <div class="card-body p-0">


                {{-- Cambio de contraseña --}}
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>

                        <h5 class="mb-1">
                            Permitir Cambio de Contraseña
                        </h5>

                        <small class="text-secondary">
                            Permitir que los usuarios modifiquen su contraseña.
                        </small>

                    </div>


                    <div class="custom-control custom-switch">

                        <input type="checkbox" class="custom-control-input" id="change_password"
                            wire:model.live="change_password">

                        <label class="custom-control-label" for="change_password"></label>

                    </div>

                </div>


            </div>

        </x-card>
    @endif



    {{-- ========================================================= --}}
    {{-- CONFIGURACION DE EXPERIENCIA --}}
    {{-- ========================================================= --}}

    @if ($section === 'experience')
        <div class="mb-3">

            <button class="btn btn-secondary" wire:click="$set('section', null)">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </button>

        </div>


        <x-card>

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-graduation-cap mr-2"></i>

                    Experiencia de Usuario

                </h3>

            </div>


            <div class="card-body p-0">


                {{-- Tutorial --}}
                <div class="d-flex justify-content-between align-items-center p-3">

                    <div>

                        <h5 class="mb-1">
                            Mostrar Tutorial al Iniciar
                        </h5>

                        <small class="text-secondary">
                            Mostrar el tutorial de uso al iniciar el sistema.
                        </small>

                    </div>


                    <div class="custom-control custom-switch">

                        <input type="checkbox" class="custom-control-input" id="show_tutorial"
                            wire:model.live="show_tutorial">

                        <label class="custom-control-label" for="show_tutorial"></label>

                    </div>

                </div>


            </div>

        </x-card>
    @endif



    {{-- ========================================================= --}}
    {{-- CONFIGURACION DEL SISTEMA --}}
    {{-- ========================================================= --}}

    @if ($section === 'system')
        <div class="mb-3">

            <button class="btn btn-secondary" wire:click="$set('section', null)">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </button>

        </div>


        <x-card>

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-cogs mr-2"></i>

                    Configuración del Sistema

                </h3>

            </div>


            <div class="card-body">


                <div class="form-group">

                    <label>Versión Actual</label>

                    <input type="text" class="form-control" wire:model="version">

                </div>


            </div>

        </x-card>
    @endif
    @if ($section === 'appearance')
        <div class="mb-3">

            <button class="btn btn-secondary" wire:click="$set('section', null)">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver
            </button>

        </div>


        <x-card>

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-palette mr-2"></i>

                    Apariencia

                </h3>

            </div>


            <div class="card-body">

                <h5 class="mb-3">
                    Tema
                </h5>

                <div class="row">


                    {{-- VENDEX --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <div class="card theme-card
                            {{ $theme === 'vendex' ? 'theme-selected' : '' }}"
                            wire:click="setTheme('vendex')">

                            <div class="theme-preview theme-preview-vendex"></div>

                            <div class="card-body text-center">

                                <h5 class="mb-1">
                                    Vendex
                                </h5>

                                <small class="text-secondary">
                                    Tema predeterminado
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- MIDNIGHT --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <div class="card theme-card
                            {{ $theme === 'midnight' ? 'theme-selected' : '' }}"
                            wire:click="setTheme('midnight')">

                            <div class="theme-preview theme-preview-midnight"></div>

                            <div class="card-body text-center">

                                <h5 class="mb-1">
                                    Midnight
                                </h5>

                                <small class="text-secondary">
                                    Azul oscuro
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- EMERALD --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <div class="card theme-card
                            {{ $theme === 'emerald' ? 'theme-selected' : '' }}"
                            wire:click="setTheme('emerald')">

                            <div class="theme-preview theme-preview-emerald"></div>

                            <div class="card-body text-center">

                                <h5 class="mb-1">
                                    Emerald
                                </h5>

                                <small class="text-secondary">
                                    Verde
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- PURPLE --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <div class="card theme-card
                            {{ $theme === 'purple' ? 'theme-selected' : '' }}"
                            wire:click="setTheme('purple')">

                            <div class="theme-preview theme-preview-purple"></div>

                            <div class="card-body text-center">

                                <h5 class="mb-1">
                                    Purple
                                </h5>

                                <small class="text-secondary">
                                    Morado
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- CRIMSON --}}
                    <div class="col-lg-3 col-md-6 mb-3">

                        <div class="card theme-card
                            {{ $theme === 'crimson' ? 'theme-selected' : '' }}"
                            wire:click="setTheme('crimson')">

                            <div class="theme-preview theme-preview-crimson"></div>

                            <div class="card-body text-center">

                                <h5 class="mb-1">
                                    Crimson
                                </h5>

                                <small class="text-secondary">
                                    Rojo
                                </small>

                            </div>

                        </div>

                    </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-center">
                    <button class="btn btn-primary w-50" wire:click="saveTheme">Guardar</button>
                </div>
            </div>

            </div>

        </x-card>
    @endif
</div>

@assets
<style>
/* ==========================================================
   THEME SELECTOR
   ========================================================== */

.theme-card {

    cursor: pointer;

    border: 2px solid transparent;

    transition:
        transform .2s,
        border-color .2s;

}

.theme-card:hover {

    transform: translateY(-4px);

}

.theme-selected {

    border-color: var(--color-acento) !important;

    box-shadow:
        0 0 0 2px var(--color-acento-soft);

}


/* Preview */

.theme-preview {

    height: 80px;

    border-radius:
        14px 14px 0 0;

}


/* Vendex */

.theme-preview-vendex {

    background:
        linear-gradient(
            135deg,
            #0F172A,
            #1E293B
        );

}


/* Midnight */

.theme-preview-midnight {

    background:
        linear-gradient(
            135deg,
            #020617,
            #0284C7
        );

}


/* Emerald */

.theme-preview-emerald {

    background:
        linear-gradient(
            135deg,
            #06140F,
            #10B981
        );

}


/* Purple */

.theme-preview-purple {

    background:
        linear-gradient(
            135deg,
            #120C1A,
            #8B5CF6
        );

}


/* Crimson */

.theme-preview-crimson {

    background:
        linear-gradient(
            135deg,
            #120709,
            #E11D48
        );

}


/* Light */

.theme-preview-light {

    background:
        linear-gradient(
            135deg,
            #F1F5F9,
            #4F46E5
        );

}
</style>
@endassets
