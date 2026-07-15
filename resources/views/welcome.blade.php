<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Vendex</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <div class="container py-5">

        <!-- Hero -->
        <div class="row align-items-center mb-5">

            <div class="col-lg-6">

                <h1 class="display-3 fw-bold text-primary">
                    VENDEX
                </h1>

                <p class="lead mt-4">
                    La plataforma moderna para administrar inventarios, ventas,
                    compras y almacenes desde un solo lugar.
                </p>

                <p class="text-muted">
                    Diseñada para pequeñas y grandes empresas que necesitan
                    controlar sus productos de forma rápida, segura y eficiente.
                </p>

                <div class="mt-4">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">
                        Ingresar
                    </a>

                    <a href="#caracteristicas" class="btn btn-outline-primary btn-lg">
                        Conocer más
                    </a>
                </div>

            </div>

            <div class="col-lg-6 text-center">

                <img
                    src=https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkjTOq5X5Pgjza4nKhrTEq5kHxRAYEWx4Xu2alVGQyYA&s=10""
                    class="img-fluid"
                    style="max-height:450px;">

            </div>

        </div>

        <!-- Características -->

        <section id="caracteristicas" class="py-5">

            <div class="text-center mb-5">

                <h2 class="fw-bold">
                    Todo lo que necesitas para tu negocio
                </h2>

                <p class="text-muted">
                    Una solución completa para la administración empresarial.
                </p>

            </div>

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body text-center">

                            <i class="fa fa-box fa-3x text-primary mb-3"></i>

                            <h4>Inventario</h4>

                            <p class="text-muted">
                                Controla existencias por almacén, categorías,
                                marcas y productos.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body text-center">

                            <i class="fa fa-shopping-cart fa-3x text-success mb-3"></i>

                            <h4>Ventas</h4>

                            <p class="text-muted">
                                Registra ventas, genera comprobantes
                                y consulta el historial en segundos.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body text-center">

                            <i class="fa fa-chart-line fa-3x text-warning mb-3"></i>

                            <h4>Reportes</h4>

                            <p class="text-muted">
                                Analiza ingresos, movimientos,
                                productos más vendidos y estadísticas.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body text-center">

                            <i class="fa fa-barcode fa-3x text-danger mb-3"></i>

                            <h4>Códigos de Barras</h4>

                            <p class="text-muted">
                                Genera e imprime etiquetas para una
                                identificación rápida de productos.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body text-center">

                            <i class="fa fa-warehouse fa-3x text-info mb-3"></i>

                            <h4>Almacenes</h4>

                            <p class="text-muted">
                                Administra múltiples sucursales y
                                controla el stock de cada una.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-body text-center">

                            <i class="fa fa-shield-alt fa-3x text-secondary mb-3"></i>

                            <h4>Seguridad</h4>

                            <p class="text-muted">
                                Usuarios, roles y permisos para proteger
                                toda la información de tu empresa.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- CTA -->

        <section class="py-5">

            <div class="card bg-primary text-white border-0">

                <div class="card-body text-center py-5">

                    <h2 class="fw-bold">
                        Comienza a gestionar tu negocio con VENDEX
                    </h2>

                    <p class="lead">
                        Control total de inventarios, ventas y almacenes desde una
                        plataforma rápida, intuitiva y segura.
                    </p>

                    <a href="https://wa.me/59161813282?text=Quisiera%20una%20demo%20de%20Vendex"
                       class="btn btn-light btn-lg mt-3">

                        Obtener Demo

                    </a>

                </div>

            </div>

        </section>

    </div>
    </body>
</html>
