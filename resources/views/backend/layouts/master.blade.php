<!doctype html>
<html class="no-js" lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Laravel Role Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    

    <!-- Bootstrap CSS / JS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- Partials CSS -->
    @include('backend.layouts.partials.styles')
    @yield('styles')

    <style>
        /* Estilo global para inputs, selects y textareas con la clase form-control */
        input.form-control,
        select.form-control,
        textarea.form-control {
            border: 3px solid #033a0f; /* Borde de 3px color oscuro */
            border-radius: 4px;
            padding: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        /* Efecto al hacer focus */
        input.form-control:focus,
        select.form-control:focus,
        textarea.form-control:focus {
            border-color: #0d0f0e; /* Borde más oscuro al enfocar */
            box-shadow: 0 0 0 0.2rem rgba(0, 177, 79, 0.25);
        }
        /* Estilos personalizados para tarjetas y tablas */
        .card {
            border: 3px solid #0a141f !important; /* Borde azul de 3px */
            border-radius: 5px;
        }
        .table-bordered {
            border: 2px solid #031407 !important; /* Borde verde para tablas */
        }
        .table-bordered th,
        .table-bordered td {
            border: 2px solid #052b0e !important;
        }
        /* Zebra striping alternado (fondo claro y oscuro) */
        table tr:nth-child(odd) {
            background-color: #ffffff !important;
        }
        table tr:nth-child(even) {
            background-color: #59665a !important; /* tono menos intenso que el anterior */
        }
        /* Asegúrate de que el texto contraste bien */
        table tr:nth-child(even) td {
            color: #000 !important;
        }
        /* ==========================
           Ajuste ancho select DataTables
        ========================== */
        .dataTables_wrapper .dataTables_length select {
            width: auto !important;
            min-width: 3ch;    /* espacio para hasta “100” o más */
            padding: 0.375rem 0.75rem;
            display: inline-block;
            box-sizing: content-box;
        }
    </style>
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">
            You are using an <strong>outdated</strong> browser. Please
            <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.
        </p>
    <![endif]-->

    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->

    <!-- page container area start -->
    <div class="page-container">
        @include('backend.layouts.partials.sidebar')

        <!-- main content area start -->
        <div class="main-content">
            @include('backend.layouts.partials.header')
            @yield('admin-content')
        </div>
        <!-- main content area end -->

        @include('backend.layouts.partials.footer')
    </div>
    <!-- page container area end -->

    @include('backend.layouts.partials.offsets')
    @include('backend.layouts.partials.scripts')
    @yield('scripts')
</body>

</html>
