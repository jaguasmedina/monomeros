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
                    <li><span>Información PCP</span></li>
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
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">{{ __('Identificador') }}</th>
                                    <th width="5%">{{ __('Tipo') }}</th>
                                    <th width="10%">{{ __('Nombre') }}</th>
                                    <th width="10%">{{ __('Empresa') }}</th>
                                    <th width="10%">{{ __('Fecha Registro') }}</th>
                                    <th width="10%">{{ __('Fecha Vigencia') }}</th>
                                    <th width="10%">{{ __('Cargo') }}</th>
                                    <th width="10%">{{ __('Estado') }}</th>
                                    <th width="10%">{{ __('Opciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($informations as $information)
                               <tr>
                                    <td>{{ $information->identificador }}</td>
                                    <td>{{ $information->tipo }}</td>
                                    <td>{{ $information->nombre_completo }}</td>
                                    <td>{{ $information->empresa }}</td>
                                    <td>{{ $information->fecha_registro }}</td>
                                    <td>{{ $information->fecha_vigencia }}</td>
                                    <td>{{ $information->cargo }}</td>
                                    <td>{{ $information->estado }}</td>
                                    <td>
                                        @if (auth()->user()->can('dashboard.edit'))
                                        <a class="btn btn-success text-white" href="{{ route('admin.informations.edit', $information->identificador) }}">Editar</a>
                                        @endif
                                        @if (auth()->user()->can('dashboard.delete'))
                                        <a class="btn btn-danger text-white" href="javascript:void(0);"
                                        onclick="event.preventDefault(); if(confirm('Estas seguro que deseas eliminar este registro?')) { document.getElementById('delete-form-{{ $information->identificador }}').submit(); }">
                                            {{ __('Eliminar') }}
                                        </a>
                                        <form id="delete-form-{{ $information->identificador }}" action="{{ route('admin.informations.destroy', $information->identificador) }}" method="POST" style="display: none;">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        @endif
                                    </td>
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