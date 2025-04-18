@extends('backend.layouts.master')

@section('title', 'Reporte de Información')

@section('styles')
<!-- Puedes agregar estilos adicionales si lo deseas -->
<style>
    .filter-form {
        margin-bottom: 20px;
    }
    .filter-form .form-group {
        margin-right: 15px;
    }
    .btn-download {
        margin-top: 25px;
    }
</style>
@endsection

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Reporte de Información</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <!-- Filtro y botones -->
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.reports.informacion') }}" method="GET" class="form-inline filter-form">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control ml-2" value="{{ $filters['fecha_inicio'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin" class="ml-3">Fecha Fin:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control ml-2" value="{{ $filters['fecha_fin'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="empresa" class="ml-3">Empresa:</label>
                            <input type="text" name="empresa" id="empresa" class="form-control ml-2" placeholder="Empresa" value="{{ $filters['empresa'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="estado" class="ml-3">Estado:</label>
                            <select name="estado" id="estado" class="form-control ml-2">
                                <option value="">-- Todos --</option>
                                <option value="FAVORABLE" {{ (isset($filters['estado']) && $filters['estado'] == 'FAVORABLE') ? 'selected' : '' }}>FAVORABLE</option>
                                <option value="NO FAVORABLE" {{ (isset($filters['estado']) && $filters['estado'] == 'NO FAVORABLE') ? 'selected' : '' }}>NO FAVORABLE</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary ml-3">Filtrar</button>
                        <!-- Botón para descargar Excel, se envían los mismos parámetros de filtro -->
                        <a href="{{ route('admin.reports.informacion.export', request()->query()) }}" class="btn btn-success ml-3 btn-download">Descargar Excel</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resultados del reporte -->
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Resultados del Reporte</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Identificador</th>
                                    <th>Tipo</th>
                                    <th>Nombre Completo</th>
                                    <th>Empresa</th>
                                    <th>Fecha Registro</th>
                                    <th>Fecha Vigencia</th>
                                    <th>Cargo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($informacion as $info)
                                    <tr>
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
                                @if($informacion->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center">No se encontraron registros.</td>
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
