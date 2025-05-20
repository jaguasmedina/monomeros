@extends('backend.layouts.master')

@section('title')
Dashboard - Panel administrador
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Aréa de Trabajo</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="index.html">Inicio</a></li>
                    <li><span>INFORMACIÓN PCP</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">{{ __('Información PCP') }}</h4>
                    <p class="float-right mb-2">
                        @if (auth()->user()->can('dashboard.edit'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.informations.create') }}">
                                {{ __('Crear Registro') }}
                            </a>
                        @endif
                        
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead>
                                <tr>
                                  <th>ID SOLICITUD</th>
                                  <th>IDENTIFICADOR</th>
                                  <th>TIPO</th>
                                  <th>NOMBRE</th>
                                  <th>EMPRESA</th>
                                  <th>FECHA REGISTRO</th>
                                  <th>FECHA VIGENCIA</th>
                                  <th>CARGO</th>
                                  <th>ESTADO</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($informations as $info)
                                  <tr>
                                    <td>{{ $info->solicitud->id ?? '—' }}</td>
                                    <td>{{ $info->identificador }}</td>
                                    <td>{{ $info->tipo }}</td>
                                    <td>{{ $info->nombre_completo }}</td>
                                    <td>{{ $info->empresa }}</td>
                                    <td>{{ $info->fecha_registro }}</td>
                                    <td>{{ $info->fecha_vigencia }}</td>
                                    <td>{{ $info->cargo }}</td>
                                    <td>{{ $info->estado }}</td>
                                  </tr>
                                @endforeach
                              </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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