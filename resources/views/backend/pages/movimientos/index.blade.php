@extends('backend.layouts.master')

@section('title', 'Historial de Movimientos')

@section('admin-content')
<div class="page-title-area">
  <h4 class="page-title">Historial de Movimientos</h4>
</div>

<form action="{{ route('admin.movimientos.index') }}" method="GET" class="mb-4">
  <div class="input-group">
    <input type="number"
           name="numero_solicitud"
           class="form-control"
           placeholder="Número de Solicitud"
           value="{{ old('numero_solicitud', $numeroSolicitud) }}"
           required>
    <div class="input-group-append">
      <button class="btn btn-primary" type="submit">Buscar</button>
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

        {{-- Último estado --}}
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
