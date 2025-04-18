@extends('backend.layouts.master')

@section('title', 'Visualizador de Información')

@section('styles')
<style>
    .filter-form {
        margin-bottom: 20px;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    .filter-form .form-group {
        margin-bottom: 10px;
    }
    .filter-form label {
        font-weight: bold;
    }
    .btn-download {
        margin-top: 10px;
    }
</style>
@endsection

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Visualizador de Información</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <!-- Formulario de filtros -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.visualizador.report') }}" method="GET" class="filter-form">
                        <div class="row">
                            <!--<div class="form-group col-md-4">
                                <label for="numero_solicitud">Número de Solicitud:</label>
                                <input type="text" name="numero_solicitud" id="numero_solicitud" class="form-control" placeholder="Número de Solicitud" value="{{ $filters['numero_solicitud'] ?? '' }}">
                            </div>-->
                            <div class="form-group col-md-4">
                                <label for="nombre">Razón Social / Nombre de Persona:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Razón Social o Nombre" value="{{ $filters['nombre'] ?? '' }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="identificador">Identificador:</label>
                                <input type="text" name="identificador" id="identificador" class="form-control" placeholder="Identificador" value="{{ $filters['identificador'] ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <!-- Aquí podrías agregar un botón para exportar a Excel si así lo deseas -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados del reporte -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Resultados del Reporte</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Identificador</th>
                                    <th>Nombre Completo</th>
                                    <th>Empresa</th>
                                    <th>Fecha Registro</th>
                                    <th>Fecha Vigencia</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($informacion as $info)
                                    <tr>
                                        <td>{{ $info->tipo }}</td>
                                        <td>{{ $info->identificador }}</td>
                                        <td>{{ $info->nombre_completo }}</td>
                                        <td>{{ $info->empresa }}</td>
                                        <td>{{ $info->fecha_registro }}</td>
                                        <td>{{ $info->fecha_vigencia }}</td>
                                        <td>{{ $info->estado }}</td>
                                    </tr>
                                @endforeach
                                @if($informacion->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center">No se encontraron registros.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
