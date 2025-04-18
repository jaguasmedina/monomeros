@extends('backend.layouts.master')

@section('title', 'Reporte de Solicitudes')

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
            <h4 class="page-title">Reporte de Solicitudes</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <!-- Filtro y botones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.reports.solicitudes') }}" method="GET" class="filter-form">
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
                                <label for="razon_social">Razón Social:</label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control" placeholder="Razón Social" value="{{ $filters['razon_social'] ?? '' }}">
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
                                <a href="{{ route('admin.reports.solicitudes.export', request()->query()) }}" class="btn btn-success btn-download">Descargar Excel</a>
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
                    <h4 class="header-title">Resultados del Reporte de Solicitudes</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID Solicitud</th>
                                    <th>Tipo de Persona</th>
                                    <th>Fecha Registro</th>
                                    <th>Razón Social</th>
                                    <th>Tipo ID</th>
                                    <th>Identificador</th>
                                    <th>Motivo</th>
                                    <th>Nombre Completo</th>
                                    <th>Tipo Cliente</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                    <tr>
                                        <td>{{ $solicitud->id }}</td>
                                        <td>{{ $solicitud->tipo_persona }}</td>
                                        <td>{{ $solicitud->fecha_registro }}</td>
                                        <td>{{ $solicitud->razon_social }}</td>
                                        <td>{{ $solicitud->tipo_id }}</td>
                                        <td>{{ $solicitud->identificador }}</td>
                                        <td>{{ $solicitud->motivo }}</td>
                                        <td>{{ $solicitud->nombre_completo }}</td>
                                        <td>{{ $solicitud->tipo_cliente }}</td>
                                        <td>{{ $solicitud->estado }}</td>
                                    </tr>
                                @endforeach
                                @if($solicitudes->isEmpty())
                                    <tr>
                                        <td colspan="10" class="text-center">No se encontraron registros.</td>
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
