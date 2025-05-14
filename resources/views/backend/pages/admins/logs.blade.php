@extends('backend.layouts.master')

@section('title')
    {{ __('Administrador - Panel Administrador') }}
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection

@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{ __('Admins') }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>{{ __('All Admins') }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">{{ __('Logs') }}</h4>

                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="15%">{{ __('Modulo') }}</th>
                                    <th width="15%">{{ __('Descripción') }}</th>
                                    <th width="15%">{{ __('Acción') }}</th>
                                    <th width="15%">{{ __('Afectado') }}</th>
                                    <th width="15%">{{ __('Quien realizo la acción') }}</th>
                                    <th width="15%">{{ __('Fecha Accción') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($logs as $log)
                               <tr>
                                    <td>{{ $log->log_name }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->properties }}</td>
                                    <td>{{ optional($log->subject)->name ?? '-' }}</td>
                                    <td>{{ optional($log->causer)->name ?? '-' }}</td>
                                    <td>{{ $log->created_at }}</td>
                                   </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
    </div>
</div>
@endsection

@section('scripts')
     <!-- Start datatable js -->
     <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

     <script>
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true,
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros',
                    zeroRecords: 'No se encontraron registros',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    search: 'Buscar:',
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    },
                    loadingRecords: 'Cargando...',
                    processing:     'Procesando...',
                    emptyTable:     'No hay datos disponibles en la tabla',
                    infoThousands:  '.',
                    aria: {
                      sortAscending:  ': activar para ordenar la columna de manera ascendente',
                      sortDescending: ': activar para ordenar la columna de manera descendente'
                    }
                }
            });
        }
    </script>
@endsection
