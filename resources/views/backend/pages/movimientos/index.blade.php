@extends('backend.layouts.master')

@section('title', 'Historial de Movimientos')

@section('styles')
<style>
    /* Reducimos un poco el ancho máximo del input-group para que no ocupe todo el ancho */
    .search-row .input-group {
        max-width: 400px;
        margin: 0 auto;
    }
</style>
@endsection

@section('admin-content')
<div class="page-title-area mb-4">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Historial de Movimientos</h4>
        </div>
    </div>
</div>

{{-- Formulario de búsqueda --}}
<form action="{{ route('admin.movimientos.index') }}" method="GET">
    <div class="row search-row mb-4">
        <div class="col-12">
            <div class="input-group">
                <input 
                    type="number"
                    name="numero_solicitud"
                    class="form-control"
                    placeholder="Número de Solicitud"
                    value="{{ old('numero_solicitud', $numeroSolicitud) }}"
                    required
                >
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </div>
        </div>
    </div>
</form>

@if($numeroSolicitud)
    @if($movimientos->isEmpty())
        <div class="alert alert-warning">
            No se encontraron movimientos para la solicitud #{{ $numeroSolicitud }}.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Solicitud #{{ $numeroSolicitud }}</h4>

                @foreach($movimientos as $mov)
                    <div class="border rounded p-3 mb-3">
                        <p><strong>Fecha:</strong> {{ $mov->fecha_movimiento }}</p>
                        <p><strong>Estado Anterior:</strong> {{ strtoupper($mov->estado_anterior) }}</p>
                        <p><strong>Estado Nuevo:</strong> {{ strtoupper($mov->estado_nuevo) }}</p>
                        <p><strong>Comentario:</strong> {{ $mov->comentario ?? '—' }}</p>
                    </div>
                @endforeach

                {{-- Mostrar siempre el último estado de forma destacada --}}
                @php $ultimo = $movimientos->last(); @endphp
                <div class="alert alert-info">
                    Último Estado Nuevo:
                    <strong>{{ strtoupper($ultimo->estado_nuevo) }}</strong>
                    ({{ $ultimo->fecha_movimiento }})
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
