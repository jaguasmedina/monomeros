@extends('backend.layouts.master')

@section('title', 'Reporte de Información')

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
    .btn-download {
        margin-top: 10px;
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
    <!-- Filtro y botones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.reports.informacion') }}" method="GET" class="filter-form">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="fecha_inicio">Fecha Inicio:</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $filters['fecha_inicio'] ?? '' }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="fecha_fin">Fecha Fin:</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ $filters['fecha_fin'] ?? '' }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="empresa">Empresa:</label>
                                <input type="text" name="empresa" id="empresa" class="form-control" placeholder="Empresa" value="{{ $filters['empresa'] ?? '' }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="estado">Estado:</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="">-- Todos --</option>
                                    <option value="FAVORABLE" {{ (isset($filters['estado']) && $filters['estado'] == 'FAVORABLE') ? 'selected' : '' }}>FAVORABLE</option>
                                    <option value="NO FAVORABLE" {{ (isset($filters['estado']) && $filters['estado'] == 'NO FAVORABLE') ? 'selected' : '' }}>NO FAVORABLE</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <!-- Botón para descargar Excel, pasando los mismos parámetros -->
                                <a href="{{ route('admin.reports.informacion.export', request()->query()) }}" class="btn btn-success btn-download">Descargar Excel</a>
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
