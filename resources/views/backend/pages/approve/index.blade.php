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

@php
    // Por si no viniera $vista desde el controller
    $vista = $vista ?? 1;
@endphp

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{ __('Solicitudes') }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>{{ __('Todas las solicitudes') }}</span></li>
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
                    <h4 class="header-title float-left">{{ __('Solicitudes') }}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="15%">{{ __('Razón Social') }}</th>
                                    <th width="15%">{{ __('Fecha') }}</th>
                                    <th width="15%">{{ __('Identificador') }}</th>
                                    <th width="15%">{{ __('Motivo') }}</th>
                                    <th width="15%">{{ __('Tipo Cliente') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                             
                                @foreach ($solicitudes as $solicitud)
                                {{--  Si ya está ENTREGADO, no lo mostramos  --}}
                                    @if($solicitud->estado === 'ENTREGADO')
                                    @continue
                                @endif

                                @php
                                    // URL según rol
                                    if ($vista === 1) {
                                        $url = route('admin.approver.show', $solicitud->id) . '?vista=1';
                                    } else {
                                        $url = route('admin.approver2.show', $solicitud->id);
                                    }
                                @endphp

                                <tr onclick="window.location='{{ $url }}';" style="cursor: pointer;">
                                    <td>{{ $solicitud->razon_social }}</td>
                                    <td>{{ $solicitud->fecha_registro }}</td>
                                    <td>{{ $solicitud->tipo_id . ' ' . $solicitud->identificador }}</td>
                                    <td>{{ $solicitud->motivo }}</td>
                                    <td>{{ $solicitud->tipo_cliente }}</td>
                                    <th></th>
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
