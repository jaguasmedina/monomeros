@extends('backend.layouts.master')

@section('title', 'Reporte de Miembros')

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
            <h4 class="page-title">Reporte de Miembros</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <!-- Filtro y botones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.reports.miembros') }}" method="GET" class="filter-form">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="nombre">Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" value="{{ $filters['nombre'] ?? '' }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tipo_id">Tipo de ID:</label>
                                <select name="tipo_id" id="tipo_id" class="form-control">
                                    <option value="">-- Todos --</option>
                                    <option value="cc" {{ (isset($filters['tipo_id']) && $filters['tipo_id'] == 'cc') ? 'selected' : '' }}>C.C.</option>
                                    <option value="ce" {{ (isset($filters['tipo_id']) && $filters['tipo_id'] == 'ce') ? 'selected' : '' }}>C.E.</option>
                                    <option value="pa" {{ (isset($filters['tipo_id']) && $filters['tipo_id'] == 'pa') ? 'selected' : '' }}>P.A.</option>
                                    <option value="ppt" {{ (isset($filters['tipo_id']) && $filters['tipo_id'] == 'ppt') ? 'selected' : '' }}>PPT</option>
                                    <option value="pep" {{ (isset($filters['tipo_id']) && $filters['tipo_id'] == 'pep') ? 'selected' : '' }}>PEP</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="favorable">¿Favorable?</label>
                                <select name="favorable" id="favorable" class="form-control">
                                    <option value="">-- Todos --</option>
                                    <option value="si" {{ (isset($filters['favorable']) && $filters['favorable'] == 'si') ? 'selected' : '' }}>Sí</option>
                                    <option value="no" {{ (isset($filters['favorable']) && $filters['favorable'] == 'no') ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <a href="{{ route('admin.reports.miembros.export', request()->query()) }}" class="btn btn-success btn-download">Descargar Excel</a>
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
                    <h4 class="header-title">Resultados del Reporte de Miembros</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitud ID</th>
                                    <th>Título</th>
                                    <th>Nombre</th>
                                    <th>Tipo ID</th>
                                    <th>Número ID</th>
                                    <th>Favorable</th>
                                    <th>Concepto No Favorable</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($miembros as $miembro)
                                    <tr>
                                        <td>{{ $miembro->id }}</td>
                                        <td>{{ $miembro->solicitud_id }}</td>
                                        <td>{{ $miembro->titulo }}</td>
                                        <td>{{ $miembro->nombre }}</td>
                                        <td>{{ $miembro->tipo_id }}</td>
                                        <td>{{ $miembro->numero_id }}</td>
                                        <td>{{ $miembro->favorable }}</td>
                                        <td>{{ $miembro->concepto_no_favorable }}</td>
                                    </tr>
                                @endforeach
                                @if($miembros->isEmpty())
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
