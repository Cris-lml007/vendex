<x-slot name="header">
    <div class="d-flex align-items-center">
        <i class="fas fa-file-import mr-2"></i>
        Importar datos
    </div>
</x-slot>

<div>

    {{-- ========================================================= --}}
    {{-- CABECERA --}}
    {{-- ========================================================= --}}

    <div class="mb-4">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-1">
                    Importación de datos
                </h4>

                <p class="text-muted mb-0">
                    Importa productos, categorías y marcas desde un archivo Excel.
                </p>

            </div>

            @if ($file)
                <button type="button" class="btn btn-outline-secondary" wire:click="restart" wire:loading.attr="disabled">
                    <i class="fas fa-redo mr-1"></i>
                    Nueva importación
                </button>
            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PROGRESO --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-body py-3">

            <div class="row text-center">

                {{-- Paso 1 --}}
                <div class="col-3">

                    <div class="{{ $step >= 1 ? 'text-primary' : 'text-muted' }}">

                        <div class="mb-1">

                            <span class="badge {{ $step >= 1 ? 'badge-primary' : 'badge-secondary' }} p-2">
                                <i class="fas fa-file-excel"></i>
                            </span>

                        </div>

                        <small class="font-weight-bold">
                            Archivo
                        </small>

                    </div>

                </div>


                {{-- Paso 2 --}}
                <div class="col-3">

                    <div class="{{ $step >= 2 ? 'text-primary' : 'text-muted' }}">

                        <div class="mb-1">

                            <span class="badge {{ $step >= 2 ? 'badge-primary' : 'badge-secondary' }} p-2">
                                <i class="fas fa-columns"></i>
                            </span>

                        </div>

                        <small class="font-weight-bold">
                            Mapeo
                        </small>

                    </div>

                </div>


                {{-- Paso 3 --}}
                <div class="col-3">

                    <div class="{{ $step >= 3 ? 'text-primary' : 'text-muted' }}">

                        <div class="mb-1">

                            <span class="badge {{ $step >= 3 ? 'badge-primary' : 'badge-secondary' }} p-2">
                                <i class="fas fa-eye"></i>
                            </span>

                        </div>

                        <small class="font-weight-bold">
                            Revisar
                        </small>

                    </div>

                </div>


                {{-- Paso 4 --}}
                <div class="col-3">

                    <div class="{{ $step >= 4 ? 'text-success' : 'text-muted' }}">

                        <div class="mb-1">

                            <span class="badge {{ $step >= 4 ? 'badge-success' : 'badge-secondary' }} p-2">
                                <i class="fas fa-check"></i>
                            </span>

                        </div>

                        <small class="font-weight-bold">
                            Finalizado
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ERRORES --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <div class="d-flex align-items-start">

                <i class="fas fa-exclamation-circle mr-2 mt-1"></i>

                <div>

                    <strong>
                        No se puede continuar.
                    </strong>

                    <ul class="mb-0 mt-1 pl-3">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PASO 1 - ARCHIVO --}}
    {{-- ========================================================= --}}

    @if ($step === 1)

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-file-upload mr-2"></i>
                    Seleccionar archivo

                </h3>

            </div>


            <div class="card-body">

                <div class="alert alert-info">

                    <i class="fas fa-info-circle mr-1"></i>

                    Selecciona un archivo
                    <strong>Excel (.xlsx / .xls)</strong>
                    o
                    <strong>CSV</strong>
                    para comenzar la importación.

                </div>


                {{-- Upload --}}
                <div class="form-group">

                    <label>
                        Archivo
                    </label>

                    <input type="file" class="form-control" wire:model="file" accept=".xlsx,.xls,.csv">

                    <small class="form-text text-muted">
                        Tamaño máximo: 10 MB.
                    </small>

                </div>


                {{-- Loading upload --}}
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


                @if ($file && count($sheetNames))

                    <hr>


                    {{-- ================================================= --}}
                    {{-- HOJAS --}}
                    {{-- ================================================= --}}

                    <div class="form-group">

                        <label>

                            <i class="fas fa-table mr-1"></i>
                            Hoja del archivo

                        </label>

                        <select class="form-control" wire:model.live="selectedSheet" wire:loading.attr="disabled"
                            wire:target="selectedSheet">

                            @foreach ($sheetNames as $index => $sheetName)
                                <option value="{{ $index }}">
                                    {{ $sheetName }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Loading hoja --}}
                    <div wire:loading wire:target="selectedSheet" class="alert alert-info w-100 mb-3">

                        <div class="d-flex align-items-center">

                            <span class="spinner-border mr-3" role="status" aria-hidden="true"></span>

                            <div>

                                <strong class="d-block">
                                    Cargando hoja
                                </strong>

                                <small class="d-block">
                                    {{ $sheetNames[$selectedSheet] ?? '' }}
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- Información hoja --}}
                    <div wire:loading.remove wire:target="selectedSheet">

                        <div class="row mb-3">

                            {{-- Hoja --}}
                            <div class="col-md-4 mb-2">

                                <div class="card mb-0 bg-primary">

                                    <div class="card-body py-2 text-white">

                                        <small class="d-block">
                                            Hoja seleccionada
                                        </small>

                                        <strong>
                                            {{ $sheetNames[$selectedSheet] ?? '-' }}
                                        </strong>

                                    </div>

                                </div>

                            </div>


                            {{-- Columnas --}}
                            <div class="col-md-4 mb-2">

                                <div class="card mb-0 bg-warning">

                                    <div class="card-body py-2 text-white">

                                        <small class="d-block">
                                            Columnas
                                        </small>

                                        <strong>
                                            {{ count($headers) }}
                                        </strong>

                                    </div>

                                </div>

                            </div>


                            {{-- Registros --}}
                            <div class="col-md-4 mb-2">

                                <div class="card mb-0 bg-success">

                                    <div class="card-body py-2 text-white">

                                        <small class="d-block">
                                            Registros
                                        </small>

                                        <strong>
                                            {{ $totalRows }}
                                        </strong>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- PREVIEW EXCEL --}}
                        {{-- ================================================= --}}

                        @if (!empty($headers))

                            <div class="card">

                                <div class="card-header">

                                    <h3 class="card-title">

                                        <i class="fas fa-eye mr-2"></i>
                                        Vista previa

                                    </h3>

                                </div>


                                <div class="card-body p-0">

                                    <div class="table-responsive">

                                        <table class="table table-bordered table-hover table-sm mb-0">

                                            <thead>

                                                <tr>

                                                    @foreach ($headers as $header)
                                                        <th class="text-nowrap">
                                                            {{ $header ?: '—' }}
                                                        </th>
                                                    @endforeach

                                                </tr>

                                            </thead>


                                            <tbody>

                                                @forelse ($preview as $row)
                                                    <tr>

                                                        @foreach ($headers as $index => $header)
                                                            <td class="text-nowrap">
                                                                {{ $row[$index] ?? '' }}
                                                            </td>
                                                        @endforeach

                                                    </tr>

                                                @empty

                                                    <tr>

                                                        <td colspan="{{ max(count($headers), 1) }}"
                                                            class="text-center text-muted py-4">
                                                            No hay registros para mostrar.
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

                @endif

            </div>


            @if ($file && count($headers))
                <div class="card-footer d-flex justify-content-end">

                    <button type="button" class="btn btn-primary" wire:click="$set('step', 2)"
                        wire:loading.attr="disabled" wire:target="selectedSheet">

                        <i class="fas fa-arrow-right mr-1"></i>
                        Continuar

                    </button>

                </div>
            @endif

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PASO 2 - MAPEO --}}
    {{-- ========================================================= --}}

    @if ($step === 2)

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-columns mr-2"></i>
                    Mapear columnas

                </h3>

            </div>


            <div class="card-body">

                <div class="alert alert-info">

                    <i class="fas fa-info-circle mr-1"></i>

                    Selecciona qué columna de Excel corresponde a cada
                    campo de Vendex. Las secciones sin ningún campo
                    asignado no serán importadas.

                </div>


                {{-- ================================================= --}}
                {{-- PRODUCTOS --}}
                {{-- ================================================= --}}

                <div class="card card-primary card-outline">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="fas fa-box mr-2"></i>
                            Productos

                        </h3>


                        <div class="card-tools">

                            @if ($this->hasMapping('product'))
                                <span class="badge badge-success">
                                    Activo
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    Inactivo
                                </span>
                            @endif

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="row">

                            @foreach ([
        'id' => 'ID / Código',
        'name' => 'Nombre',
        'model' => 'Modelo',
        'description' => 'Descripción',
        'price' => 'Precio',
        'color' => 'Color',
        'parent_id' => 'ID Padre',
        'wholesale_price' => 'Precio mayorista',
        'category' => 'Categoría',
        'brand' => 'Marca',
    ] as $field => $label)
                                <div class="col-md-6 col-lg-4 mb-3">

                                    <label>
                                        {{ $label }}
                                    </label>

                                    <select class="form-control"
                                        wire:model.live="mapping.product.{{ $field }}">

                                        <option value="">
                                            No importar
                                        </option>

                                        @foreach ($headers as $index => $header)
                                            <option value="{{ $index }}">
                                                {{ $header ?: 'Columna ' . ($index + 1) }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                            @endforeach

                        </div>


                        {{-- Tags --}}
                        <div class="border-top pt-3 mt-2">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>

                                    <strong>
                                        <i class="fas fa-tags mr-1"></i>
                                        Etiquetas
                                    </strong>

                                    <small class="text-muted d-block">
                                        Cada columna seleccionada se guardará como una etiqueta del producto.
                                    </small>

                                </div>


                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    wire:click="addTagMapping">

                                    <i class="fas fa-plus mr-1"></i>
                                    Agregar etiqueta

                                </button>

                            </div>


                            @forelse ($mapping['product']['tags'] as $index => $tag)
                                <div class="input-group mb-2">

                                    <div class="input-group-prepend">

                                        <span class="input-group-text">
                                            Tag {{ $index + 1 }}
                                        </span>

                                    </div>


                                    <select class="form-control"
                                        wire:model.live="mapping.product.tags.{{ $index }}">

                                        <option value="">
                                            Seleccionar columna
                                        </option>

                                        @foreach ($headers as $headerIndex => $header)
                                            <option value="{{ $headerIndex }}">
                                                {{ $header ?: 'Columna ' . ($headerIndex + 1) }}
                                            </option>
                                        @endforeach

                                    </select>


                                    <div class="input-group-append">

                                        <button type="button" class="btn btn-outline-danger"
                                            wire:click="removeTagMapping({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>

                                    </div>

                                </div>

                            @empty

                                <small class="text-muted">
                                    No se han agregado etiquetas.
                                </small>
                            @endforelse

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- CATEGORÍAS --}}
                {{-- ================================================= --}}

                <div class="card card-success card-outline">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="fas fa-folder mr-2"></i>
                            Categorías

                        </h3>


                        <div class="card-tools">

                            @if ($this->hasMapping('category'))
                                <span class="badge badge-success">
                                    Activo
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    Inactivo
                                </span>
                            @endif

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="form-group mb-0">

                            <label>
                                Nombre
                            </label>

                            <select class="form-control" wire:model.live="mapping.category.name">

                                <option value="">
                                    No importar categorías
                                </option>

                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}">
                                        {{ $header ?: 'Columna ' . ($index + 1) }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- MARCAS --}}
                {{-- ================================================= --}}

                <div class="card card-warning card-outline">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="fas fa-tag mr-2"></i>
                            Marcas

                        </h3>


                        <div class="card-tools">

                            @if ($this->hasMapping('brand'))
                                <span class="badge badge-success">
                                    Activo
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    Inactivo
                                </span>
                            @endif

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="alert alert-warning">

                            <i class="fas fa-lightbulb mr-1"></i>

                            Las marcas referenciadas desde Productos se crearán
                            automáticamente si no existen.

                        </div>


                        <div class="form-group">

                            <label>
                                Nombre
                            </label>

                            <select class="form-control" wire:model.live="mapping.brand.name">

                                <option value="">
                                    No importar marcas
                                </option>

                                @foreach ($headers as $index => $header)
                                    <option value="{{ $index }}">
                                        {{ $header ?: 'Columna ' . ($index + 1) }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <div class="row">

                            {{-- Made --}}
                            <div class="col-md-4">

                                <label>
                                    Fabricante
                                </label>

                                <select class="form-control" wire:model.live="mapping.brand.made_type">

                                    <option value="fixed">
                                        Valor fijo
                                    </option>

                                    <option value="excel">
                                        Desde Excel
                                    </option>

                                </select>


                                @if ($mapping['brand']['made_type'] === 'excel')

                                    <select class="form-control mt-2" wire:model.live="mapping.brand.made">

                                        <option value="">
                                            Seleccionar columna
                                        </option>

                                        @foreach ($headers as $index => $header)
                                            <option value="{{ $index }}">
                                                {{ $header ?: 'Columna ' . ($index + 1) }}
                                            </option>
                                        @endforeach

                                    </select>
                                @else
                                    <input type="text" class="form-control mt-2"
                                        wire:model.live="mapping.brand.made" placeholder="GENERICO">

                                @endif

                            </div>


                            {{-- Background --}}
                            <div class="col-md-4">

                                <label>
                                    Color de fondo
                                </label>

                                <select class="form-control" wire:model.live="mapping.brand.bg_color_type">

                                    <option value="fixed">
                                        Valor fijo
                                    </option>

                                    <option value="excel">
                                        Desde Excel
                                    </option>

                                </select>


                                @if ($mapping['brand']['bg_color_type'] === 'excel')

                                    <select class="form-control mt-2" wire:model.live="mapping.brand.bg_color">

                                        <option value="">
                                            Seleccionar columna
                                        </option>

                                        @foreach ($headers as $index => $header)
                                            <option value="{{ $index }}">
                                                {{ $header ?: 'Columna ' . ($index + 1) }}
                                            </option>
                                        @endforeach

                                    </select>
                                @else
                                    <input type="text" class="form-control mt-2"
                                        wire:model.live="mapping.brand.bg_color" placeholder="white">

                                @endif

                            </div>


                            {{-- Foreground --}}
                            <div class="col-md-4">

                                <label>
                                    Color de texto
                                </label>

                                <select class="form-control" wire:model.live="mapping.brand.fb_color_type">

                                    <option value="fixed">
                                        Valor fijo
                                    </option>

                                    <option value="excel">
                                        Desde Excel
                                    </option>

                                </select>


                                @if ($mapping['brand']['fb_color_type'] === 'excel')

                                    <select class="form-control mt-2" wire:model.live="mapping.brand.fb_color">

                                        <option value="">
                                            Seleccionar columna
                                        </option>

                                        @foreach ($headers as $index => $header)
                                            <option value="{{ $index }}">
                                                {{ $header ?: 'Columna ' . ($index + 1) }}
                                            </option>
                                        @endforeach

                                    </select>
                                @else
                                    <input type="text" class="form-control mt-2"
                                        wire:model.live="mapping.brand.fb_color" placeholder="black">

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card-footer d-flex justify-content-between">

                <button type="button" class="btn btn-secondary" wire:click="$set('step', 1)">

                    <i class="fas fa-arrow-left mr-1"></i>
                    Atrás

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
    {{-- PASO 3 - REVISIÓN --}}
    {{-- ========================================================= --}}

    @if ($step === 3)

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-search mr-2"></i>
                    Revisar importación

                </h3>

            </div>


            <div class="card-body">

                <div class="alert alert-warning">

                    <i class="fas fa-exclamation-triangle mr-1"></i>

                    Revisa los datos antes de continuar.

                    <strong>
                        No se modificará la base de datos hasta confirmar.
                    </strong>

                </div>


                {{-- Ir a confirmar --}}
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


                {{-- ================================================= --}}
                {{-- PRODUCTOS --}}
                {{-- ================================================= --}}

                @if (!empty($mappedData['products']))

                    <div class="card card-primary card-outline">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-box mr-2"></i>
                                Productos

                                <span class="badge badge-primary ml-2">
                                    {{ count($mappedData['products']) }}
                                </span>

                            </h3>

                        </div>


                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover table-sm mb-0">

                                    <thead>

                                        <tr>

                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Modelo</th>
                                            <th>Precio</th>
                                            <th>Mayorista</th>
                                            <th>Categoría</th>
                                            <th>Marca</th>
                                            <th>Tags</th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach ($mappedData['products'] as $product)
                                            <tr>

                                                <td>
                                                    {{ $product['id'] ?: 'Automático' }}
                                                </td>

                                                <td>
                                                    {{ $product['name'] }}
                                                </td>

                                                <td>
                                                    {{ $product['model'] ?: '-' }}
                                                </td>

                                                <td>
                                                    {{ $product['price'] ?: '-' }}
                                                </td>

                                                <td>
                                                    {{ $product['wholesale_price'] ?: '-' }}
                                                </td>

                                                <td>
                                                    {{ $product['category'] ?: '-' }}
                                                </td>

                                                <td>
                                                    {{ $product['brand'] ?: '-' }}
                                                </td>

                                                <td>

                                                    @forelse ($product['tags'] as $tag)
                                                        <span class="badge badge-info mr-1">
                                                            {{ $tag['name'] }}: {{ $tag['value'] }}
                                                        </span>

                                                    @empty

                                                        <span class="text-muted">
                                                            -
                                                        </span>
                                                    @endforelse

                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- CATEGORÍAS --}}
                {{-- ================================================= --}}

                @if (!empty($mappedData['categories']))

                    <div class="card card-success card-outline">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-folder mr-2"></i>
                                Categorías

                                <span class="badge badge-success ml-2">
                                    {{ count($mappedData['categories']) }}
                                </span>

                            </h3>

                        </div>


                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover mb-0">

                                    <thead>

                                        <tr>

                                            <th>
                                                Nombre
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach ($mappedData['categories'] as $category)
                                            <tr>

                                                <td>
                                                    {{ $category['name'] }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- MARCAS --}}
                {{-- ================================================= --}}

                @if (!empty($mappedData['brands']))

                    <div class="card card-warning card-outline">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-tag mr-2"></i>
                                Marcas

                                <span class="badge badge-warning ml-2">
                                    {{ count($mappedData['brands']) }}
                                </span>

                            </h3>

                        </div>


                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover mb-0">

                                    <thead>

                                        <tr>

                                            <th>Nombre</th>
                                            <th>Fabricante</th>
                                            <th>Fondo</th>
                                            <th>Texto</th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach ($mappedData['brands'] as $brand)
                                            <tr>

                                                <td>
                                                    {{ $brand['name'] }}
                                                </td>

                                                <td>
                                                    {{ $brand['made'] ?: 'GENERICO' }}
                                                </td>

                                                <td>
                                                    {{ $brand['bg_color'] ?: 'white' }}
                                                </td>

                                                <td>
                                                    {{ $brand['fb_color'] ?: 'black' }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- SIN DATOS --}}
                {{-- ================================================= --}}

                @if (empty($mappedData['products']) && empty($mappedData['categories']) && empty($mappedData['brands']))
                    <div class="alert alert-secondary">

                        <i class="fas fa-info-circle mr-1"></i>

                        No se encontraron registros para importar
                        con el mapeo seleccionado.

                    </div>
                @endif

            </div>


            <div class="card-footer d-flex justify-content-between">

                <button type="button" class="btn btn-secondary" wire:click="$set('step', 2)"
                    wire:loading.attr="disabled">

                    <i class="fas fa-arrow-left mr-1"></i>
                    Modificar mapeo

                </button>


                <button id="confirm-import" type="button" class="btn btn-success" wire:click="import"
                    wire:loading.attr="disabled" wire:target="import">

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
    {{-- PASO 4 - FINALIZADO --}}
    {{-- ========================================================= --}}

    @if ($step === 4)
        <div class="card">

            <div class="card-body text-center py-5">

                <div class="mb-4">

                    <span class="badge badge-success p-3">
                        <i class="fas fa-check fa-2x"></i>
                    </span>

                </div>


                <h3 class="mb-2">
                    Importación completada
                </h3>


                <p class="text-muted mb-4">
                    Los datos fueron importados correctamente a Vendex.
                </p>


                <button type="button" class="btn btn-primary" wire:click="restart">

                    <i class="fas fa-file-import mr-1"></i>
                    Realizar otra importación

                </button>

            </div>

        </div>
    @endif

</div>
