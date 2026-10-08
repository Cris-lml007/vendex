<x-slot name="header">
    <div class="d-flex align-items-center">
        <i class="fas fa-boxes mr-2"></i>
        Importar stock
    </div>
</x-slot>

<div class="container-fluid">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h4 mb-1">
                Importar stock
            </h1>

            <p class="text-muted mb-0">
                Carga existencias por producto y tienda o almacén desde un archivo Excel.
            </p>
        </div>

        @if ($step > 1)
            <button type="button" class="btn btn-outline-secondary" wire:click="restart">
                <i class="fas fa-plus mr-1"></i>
                Nueva importación
            </button>
        @endif

    </div>


    {{-- Progreso --}}
    <div class="card mb-4">
        <div class="card-body">

            <div class="row text-center">

                {{-- Paso 1 --}}
                <div class="col-md-3 mb-3 mb-md-0">

                    <div class="mb-2">
                        <span
                            class="badge
                                {{ $step >= 1 ? 'badge-primary' : 'badge-secondary' }}
                                rounded-circle p-2">
                            1
                        </span>
                    </div>

                    <strong class="{{ $step >= 1 ? 'text-primary' : 'text-muted' }}">
                        Archivo
                    </strong>

                    <small class="d-block text-muted">
                        Seleccionar Excel
                    </small>

                </div>


                {{-- Paso 2 --}}
                <div class="col-md-3 mb-3 mb-md-0">

                    <div class="mb-2">
                        <span
                            class="badge
                                {{ $step >= 2 ? 'badge-primary' : 'badge-secondary' }}
                                rounded-circle p-2">
                            2
                        </span>
                    </div>

                    <strong class="{{ $step >= 2 ? 'text-primary' : 'text-muted' }}">
                        Mapeo
                    </strong>

                    <small class="d-block text-muted">
                        Relacionar columnas
                    </small>

                </div>


                {{-- Paso 3 --}}
                <div class="col-md-3 mb-3 mb-md-0">

                    <div class="mb-2">
                        <span
                            class="badge
                                {{ $step >= 3 ? 'badge-success' : 'badge-secondary' }}
                                rounded-circle p-2">
                            3
                        </span>
                    </div>

                    <strong class="{{ $step >= 3 ? 'text-success' : 'text-muted' }}">
                        Revisar
                    </strong>

                    <small class="d-block text-muted">
                        Validar información
                    </small>

                </div>


                {{-- Paso 4 --}}
                <div class="col-md-3">

                    <div class="mb-2">
                        <span
                            class="badge
                                {{ $step >= 4 ? 'badge-success' : 'badge-secondary' }}
                                rounded-circle p-2">
                            4
                        </span>
                    </div>

                    <strong class="{{ $step >= 4 ? 'text-success' : 'text-muted' }}">
                        Finalizado
                    </strong>

                    <small class="d-block text-muted">
                        Importación completada
                    </small>

                </div>

            </div>

        </div>
    </div>


    {{-- Errores --}}
    @if ($errors->any())
        <div class="alert alert-danger">

            <div class="d-flex">

                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>

                <div>
                    <strong>Se encontraron errores</strong>

                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- PASO 1 --}}
    {{-- ========================================================= --}}
    @if ($step === 1)

        <div class="card card-primary card-outline">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-excel mr-1"></i>
                    Seleccionar archivo
                </h3>
            </div>

            <div class="card-body">

                <div class="form-group">

                    <label>
                        Archivo Excel
                    </label>

                    <div class="custom-file">

                        <input type="file" class="custom-file-input" id="stock-file" wire:model="file"
                            accept=".xlsx,.xls,.csv">

                        <label class="custom-file-label" for="stock-file">
                            {{ $file ? $file->getClientOriginalName() : 'Seleccionar archivo...' }}
                        </label>

                    </div>

                    <small class="form-text text-muted">
                        Formatos permitidos: XLSX, XLS y CSV. Máximo 10 MB.
                    </small>

                </div>


                {{-- Loading archivo --}}
                <div wire:loading wire:target="file" class="alert alert-secondary w-100 mb-3">
                    <div class="d-flex align-items-center">

                        <span class="spinner-border mr-3" role="status" aria-hidden="true"></span>

                        <div>

                            <strong class="d-block">
                                Procesando archivo
                            </strong>

                            <small>
                                Subiendo y leyendo el archivo Excel...
                            </small>

                        </div>

                    </div>
                </div>


                {{-- Selección de hoja --}}
                @if (count($sheetNames) > 1)

                    <div class="form-group">

                        <label>
                            Hoja a importar
                        </label>

                        <select class="form-control" wire:model.live="selectedSheet">

                            <option value="">
                                Seleccionar hoja...
                            </option>

                            @foreach ($sheetNames as $sheetName)
                                <option value="{{ $sheetName }}">
                                    {{ $sheetName }}
                                </option>
                            @endforeach

                        </select>

                        <small class="form-text text-muted">
                            Selecciona la hoja que contiene la información del stock.
                        </small>

                    </div>


                    {{-- Loading hoja --}}
                    <div wire:loading wire:target="selectedSheet" class="alert alert-secondary">
                        <div class="d-flex align-items-center">

                            <span class="spinner-border spinner-border-sm mr-2" role="status"></span>

                            Leyendo hoja seleccionada...

                        </div>
                    </div>
                @elseif (count($sheetNames) === 1)
                    <div class="alert alert-info">

                        <i class="fas fa-file-excel mr-1"></i>

                        Hoja detectada:

                        <strong>
                            {{ $sheetNames[0] }}
                        </strong>

                    </div>

                @endif


                @if (count($headers) > 0)

                    {{-- Estadísticas --}}
                    <div class="row mb-4">

                        <div class="col-md-4 mb-3 mb-md-0">

                            <div class="small-box bg-primary mb-0">

                                <div class="inner text-white">

                                    <h3>
                                        {{ $totalRows }}
                                    </h3>

                                    <p>
                                        Registros detectados
                                    </p>

                                </div>

                                <div class="icon">
                                    <i class="fas fa-list"></i>
                                </div>

                            </div>

                        </div>


                        <div class="col-md-4 mb-3 mb-md-0">

                            <div class="small-box bg-warning mb-0">

                                <div class="inner text-white">

                                    <h3>
                                        {{ count($headers) }}
                                    </h3>

                                    <p>
                                        Columnas detectadas
                                    </p>

                                </div>

                                <div class="icon">
                                    <i class="fas fa-columns"></i>
                                </div>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="small-box bg-success mb-0">

                                <div class="inner text-white">

                                    <h3>
                                        {{ count($sheetNames) }}
                                    </h3>

                                    <p>
                                        Hojas disponibles
                                    </p>

                                </div>

                                <div class="icon">
                                    <i class="fas fa-file-excel"></i>
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Vista previa --}}
                    <div class="card card-secondary card-outline">

                        <div class="card-header">

                            <h3 class="card-title">
                                Vista previa del archivo
                            </h3>

                            <div class="card-tools">
                                <span class="badge badge-secondary">
                                    Primeras {{ count($preview) }} filas
                                </span>
                            </div>

                        </div>

                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover table-sm mb-0">

                                    <thead>
                                        <tr>
                                            @foreach ($headers as $header)
                                                <th>
                                                    {{ $header ?: 'Sin nombre' }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse ($preview as $row)
                                            <tr>

                                                @foreach ($headers as $index => $header)
                                                    <td>
                                                        {{ $row[$index] ?? '' }}
                                                    </td>
                                                @endforeach

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="{{ count($headers) }}" class="text-center text-muted">
                                                    No existen registros para mostrar.
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PASO 2 --}}
    {{-- ========================================================= --}}
    @if ($step === 2)

        <div class="card card-primary card-outline">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-columns mr-1"></i>
                    Mapear columnas
                </h3>

            </div>

            <div class="card-body">

                <div class="alert alert-info">

                    <div class="d-flex">

                        <i class="fas fa-info-circle mr-2 mt-1"></i>

                        <div>

                            <strong>
                                Configura las columnas del stock
                            </strong>

                            <p class="mb-0 mt-1">
                                Relaciona cada campo de Vendex con la columna correspondiente
                                del archivo Excel.
                                El producto debe existir previamente en Vendex.
                                Las tiendas o almacenes nuevos podrán completarse antes de importar.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="row">

                    {{-- Producto --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                ID / Código del producto
                                <span class="text-danger">*</span>
                            </label>

                            <select class="form-control" wire:model="mapping.stock.product_id">

                                <option value="">
                                    Seleccionar columna...
                                </option>

                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}">
                                        {{ $header ?: 'Columna ' . ($index + 1) }}
                                    </option>
                                @endforeach

                            </select>

                            <small class="form-text text-muted">
                                Debe corresponder al ID del producto existente en Vendex.
                            </small>

                            @error('mapping.stock.product_id')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>


                    {{-- Tienda --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Tienda / Almacén
                                <span class="text-danger">*</span>
                            </label>

                            <select class="form-control" wire:model="mapping.stock.store">

                                <option value="">
                                    Seleccionar columna...
                                </option>

                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}">
                                        {{ $header ?: 'Columna ' . ($index + 1) }}
                                    </option>
                                @endforeach

                            </select>

                            <small class="form-text text-muted">
                                El nombre debe coincidir con una tienda o almacén existente,
                                o será necesario completar sus datos en la revisión.
                            </small>

                            @error('mapping.stock.store')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>


                    {{-- Cantidad --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Cantidad
                                <span class="text-danger">*</span>
                            </label>

                            <select class="form-control" wire:model="mapping.stock.quantity">

                                <option value="">
                                    Seleccionar columna...
                                </option>

                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}">
                                        {{ $header ?: 'Columna ' . ($index + 1) }}
                                    </option>
                                @endforeach

                            </select>

                            <small class="form-text text-muted">
                                Cantidad que será agregada al stock actual.
                            </small>

                            @error('mapping.stock.quantity')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>


                    {{-- Precio compra --}}
                    <div class="col-md-6">

                        <div class="form-group">

                            <label>
                                Precio de compra
                            </label>

                            <select class="form-control" wire:model="mapping.stock.purchase_price">

                                <option value="">
                                    No importar / Sin columna
                                </option>

                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}">
                                        {{ $header ?: 'Columna ' . ($index + 1) }}
                                    </option>
                                @endforeach

                            </select>

                            <small class="form-text text-muted">
                                Campo opcional. Se utilizará como precio del movimiento de entrada.
                            </small>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card-footer d-flex justify-content-between">

                <button type="button" class="btn btn-outline-secondary" wire:click="restart">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Cancelar
                </button>


                <button type="button" class="btn btn-primary" wire:click="previewImport"
                    wire:loading.attr="disabled" wire:target="previewImport">

                    <span wire:loading.remove wire:target="previewImport">

                        <i class="fas fa-eye mr-1"></i>
                        Generar vista previa

                    </span>

                    <span wire:loading wire:target="previewImport">

                        <span class="spinner-border spinner-border-sm mr-2"></span>
                        Procesando...

                    </span>

                </button>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PASO 3 --}}
    {{-- ========================================================= --}}
    @if ($step === 3)

        {{-- Ir a confirmar: DEBE ESTAR ARRIBA --}}
        <div class="d-flex justify-content-end mb-3">

            <button type="button" class="btn btn-outline-success"
                onclick="document.getElementById('confirm-import').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    })">

                <i class="fas fa-arrow-down mr-1"></i>
                Ir a confirmar

            </button>

        </div>


        {{-- Información --}}
        <div class="alert alert-warning">

            <div class="d-flex">

                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>

                <div>

                    <strong>
                        Revisa los registros antes de importar.
                    </strong>

                    <p class="mb-0 mt-1">
                        Los registros marcados como
                        <strong>No importar</strong>
                        no serán modificados.
                        El stock de los registros válidos se sumará al stock existente.
                    </p>

                </div>

            </div>

        </div>


        {{-- Estadísticas --}}
        @php
            $validCount = collect($mappedData['stocks'] ?? [])
                ->where('valid', true)
                ->count();

            $invalidCount = collect($mappedData['stocks'] ?? [])
                ->where('valid', false)
                ->count();

            $totalCount = count($mappedData['stocks'] ?? []);
        @endphp

        <div class="row mb-4">

            <div class="col-md-4 mb-3 mb-md-0">

                <div class="small-box bg-primary mb-0">

                    <div class="inner text-white">

                        <h3>
                            {{ $totalCount }}
                        </h3>

                        <p>
                            Registros
                        </p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>

                </div>

            </div>


            <div class="col-md-4 mb-3 mb-md-0">

                <div class="small-box bg-success mb-0">

                    <div class="inner text-white">

                        <h3>
                            {{ $validCount }}
                        </h3>

                        <p>
                            Listos para importar
                        </p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="small-box bg-warning mb-0">

                    <div class="inner text-white">

                        <h3>
                            {{ $invalidCount }}
                        </h3>

                        <p>
                            No se importarán
                        </p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-exclamation"></i>
                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- NUEVAS TIENDAS --}}
        {{-- ===================================================== --}}
        @if (count($newStores) > 0)

            <div class="card card-warning card-outline">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-store mr-1"></i>

                        Nuevas tiendas / almacenes

                        <span class="badge badge-warning ml-2">
                            {{ count($newStores) }}
                        </span>

                    </h3>

                </div>

                <div class="card-body">

                    <div class="alert alert-warning">

                        <i class="fas fa-info-circle mr-1"></i>

                        Se detectaron tiendas o almacenes que no existen en Vendex.
                        Completa sus datos para poder importarlos.

                    </div>


                    @foreach ($newStores as $index => $store)
                        <div class="card card-light mb-3">

                            <div class="card-header">

                                <h3 class="card-title">

                                    <i class="fas fa-store mr-1"></i>

                                    {{ $store['name'] }}

                                </h3>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    {{-- Nombre --}}
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Nombre
                                            </label>

                                            <input type="text" class="form-control" value="{{ $store['name'] }}"
                                                readonly>

                                        </div>

                                    </div>


                                    {{-- Tipo --}}
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Tipo
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                class="form-control @error('newStores.' . $index . '.type') is-invalid @enderror"
                                                wire:model="newStores.{{ $index }}.type">

                                                <option value="">
                                                    Seleccionar tipo...
                                                </option>

                                                <option value="{{ \App\Enums\Type::STORE->value }}">
                                                    Tienda
                                                </option>

                                                <option value="{{ \App\Enums\Type::WAREHOUSE->value }}">
                                                    Almacén
                                                </option>

                                            </select>

                                            @error('newStores.' . $index . '.type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>


                                    {{-- Estado --}}
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Estado
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                class="form-control @error('newStores.' . $index . '.status') is-invalid @enderror"
                                                wire:model="newStores.{{ $index }}.status">

                                                <option value="{{ \App\Enums\Status::ACTIVE->value }}">
                                                    Activo
                                                </option>

                                                <option value="{{ \App\Enums\Status::BLOCKED->value }}">
                                                    Bloqueado
                                                </option>

                                                <option value="{{ \App\Enums\Status::DISABLE->value }}">
                                                    Deshabilitado
                                                </option>

                                            </select>

                                            @error('newStores.' . $index . '.status')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>


                                    {{-- Dirección --}}
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Dirección
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                class="form-control @error('newStores.' . $index . '.address') is-invalid @enderror"
                                                wire:model="newStores.{{ $index }}.address"
                                                placeholder="Dirección">

                                            @error('newStores.' . $index . '.address')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>


                                    {{-- Teléfono --}}
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Teléfono
                                            </label>

                                            <input type="text" class="form-control"
                                                wire:model="newStores.{{ $index }}.phone"
                                                placeholder="Teléfono">

                                        </div>

                                    </div>


                                    {{-- Email --}}
                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Correo electrónico
                                            </label>

                                            <input type="email"
                                                class="form-control @error('newStores.' . $index . '.email') is-invalid @enderror"
                                                wire:model="newStores.{{ $index }}.email"
                                                placeholder="correo@ejemplo.com">

                                            @error('newStores.' . $index . '.email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>
        @else
            <div class="alert alert-success">

                <i class="fas fa-check-circle mr-1"></i>

                Todas las tiendas y almacenes del archivo ya existen en Vendex.

            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- REVISIÓN DE STOCK --}}
        {{-- ===================================================== --}}
        <div class="card card-primary card-outline">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-boxes mr-1"></i>

                    Registros de stock

                </h3>

                <div class="card-tools">

                    <span class="badge badge-secondary">
                        {{ $totalCount }} registros
                    </span>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-sm mb-0">

                        <thead>

                            <tr>

                                <th style="width: 100px;">
                                    Estado
                                </th>

                                <th>
                                    Producto
                                </th>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Tienda / Almacén
                                </th>

                                <th>
                                    Cantidad
                                </th>

                                <th>
                                    Precio compra
                                </th>

                                <th>
                                    Resultado
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($mappedData['stocks'] as $stock)
                                <tr>

                                    {{-- Estado --}}
                                    <td>

                                        @if ($stock['valid'])
                                            <span class="badge badge-success">
                                                <i class="fas fa-check mr-1"></i>
                                                Importar
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times mr-1"></i>
                                                No importar
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Producto --}}
                                    <td>

                                        @if ($stock['product_name'] !== '')
                                            <strong>
                                                {{ $stock['product_name'] }}
                                            </strong>
                                        @else
                                            <span class="text-muted">
                                                Producto no encontrado
                                            </span>
                                        @endif

                                    </td>


                                    {{-- ID --}}
                                    <td>
                                        {{ $stock['product_id'] ?: '-' }}
                                    </td>


                                    {{-- Tienda --}}
                                    <td>

                                        @if ($stock['store'])
                                            {{ $stock['store'] }}

                                            @if ($this->isNewStore($stock['store']))
                                                <span class="badge badge-warning ml-1">
                                                    Nueva
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">
                                                -
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Cantidad --}}
                                    <td>

                                        @if ($stock['quantity'] !== '')
                                            {{ $stock['quantity'] }}
                                        @else
                                            <span class="text-muted">
                                                -
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Precio --}}
                                    <td>

                                        @if ($stock['purchase_price'] !== null && $stock['purchase_price'] !== '')
                                            {{ $stock['purchase_price'] }}
                                        @else
                                            <span class="text-muted">
                                                No indicado
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Resultado --}}
                                    <td>

                                        @if ($stock['valid'])
                                            <span class="text-success">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Registro válido
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-circle mr-1"></i>

                                                {{ $stock['reason'] }}

                                            </span>
                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center text-muted py-4">
                                        No existen registros para importar.

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Footer --}}
            <div class="card-footer d-flex justify-content-between">

                <button type="button" class="btn btn-outline-secondary" wire:click="$set('step', 2)">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Modificar mapeo
                </button>


                <button id="confirm-import" type="button" class="btn btn-success" wire:click="import"
                    wire:loading.attr="disabled" wire:target="import" @disabled($validCount === 0)>

                    <span wire:loading.remove wire:target="import">

                        <i class="fas fa-database mr-1"></i>
                        Confirmar importación

                    </span>

                    <span wire:loading wire:target="import">

                        <span class="spinner-border spinner-border-sm mr-2"></span>
                        Importando datos...

                    </span>

                </button>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PASO 4 --}}
    {{-- ========================================================= --}}
    @if ($step === 4)

        <div class="card card-success card-outline">

            <div class="card-body text-center py-5">

                <div class="mb-4">

                    <span
                        class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-check fa-2x"></i>
                    </span>

                </div>


                <h2 class="h4">
                    Importación completada
                </h2>

                <p class="text-muted mb-4">
                    El stock fue importado correctamente.
                    Las cantidades se agregaron al stock existente
                    y se registraron los movimientos correspondientes en el kardex.
                </p>


                @if (session()->has('success'))
                    <div class="alert alert-success d-inline-block text-left">

                        <i class="fas fa-check-circle mr-1"></i>

                        {{ session('success') }}

                    </div>
                @endif


                <div class="mt-3">

                    <button type="button" class="btn btn-primary" wire:click="restart">
                        <i class="fas fa-file-import mr-1"></i>
                        Realizar otra importación
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>
