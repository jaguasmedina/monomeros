@extends('backend.layouts.master')

@section('title', 'Historial de Movimientos')

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Historial de Movimientos</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <!-- Formulario para buscar movimientos por número de solicitud -->
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.movimientos.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="numero_solicitud" class="mr-2">Número de Solicitud:</label>
                            <input type="text" name="numero_solicitud" id="numero_solicitud" class="form-control"
                                   value="{{ old('numero_solicitud', $numeroSolicitud) }}"
                                   placeholder="Ej: 123">
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados: tabla con los movimientos -->
    @if($movimientos->count() > 0)
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Historial de la Solicitud #{{ $numeroSolicitud }}</h4>
                    <ul class="list-group">
                        @foreach($movimientos as $mov)
                            <li class="list-group-item">
                                <strong>Fecha:</strong> {{ $mov->fecha_movimiento }} <br>
                                <strong>Estado Anterior:</strong> {{ $mov->estado_anterior ?? 'N/A' }} <br>
                                <strong>Estado Nuevo:</strong> {{ $mov->estado_nuevo }} <br>
                                <strong>Comentario:</strong> {{ $mov->comentario ?? '---' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @elseif($numeroSolicitud)
    <div class="row">
        <div class="col-12 mt-3">
            <div class="alert alert-warning">
                No se encontraron movimientos para la solicitud #{{ $numeroSolicitud }}.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
