@extends('backend.layouts.master')

@section('title', 'Historial de Movimientos')

@section('admin-content')
<div class="page-title-area">
  <h4 class="page-title">Historial de Movimientos</h4>
</div>
<div class="main-content-inner">
  <!-- Búsqueda -->
  <form action="{{ route('admin.movimientos.index') }}" method="GET" class="form-inline mb-3">
      <label class="mr-2">Número de Solicitud:</label>
      <input name="numero_solicitud" class="form-control mr-2"
             value="{{ old('numero_solicitud', $numeroSolicitud) }}" placeholder="Ej: 123">
      <button class="btn btn-primary">Buscar</button>
  </form>

  @if($movimientos->isEmpty() && $numeroSolicitud)
    <div class="alert alert-warning">
      No se encontraron movimientos para la solicitud #{{ $numeroSolicitud }}.
    </div>
  @endif

  @if($movimientos->isNotEmpty())
    <div class="card">
      <div class="card-body">
        <h4 class="header-title">Solicitud #{{ $numeroSolicitud }}</h4>
        <ul class="list-group">
          @foreach($movimientos as $mov)
            <li class="list-group-item">
              <strong>Fecha:</strong> {{ $mov->created_at->format('d/m/Y H:i:s') }}<br>
              <strong>Estado Anterior:</strong> {{ $mov->estado_anterior ?? 'N/A' }}<br>
              <strong>Estado Nuevo:</strong> {{ strtoupper($mov->estado_nuevo) }}<br>
              <strong>Comentario:</strong> {{ $mov->comentario ?? '---' }}
            </li>
          @endforeach
        </ul>

        {{-- Destacar el más reciente --}}
        @php
          $ultimo = $movimientos->last();
        @endphp
        <div class="alert alert-info mt-3">
          <strong>Último Estado Nuevo:</strong>
          {{ strtoupper($ultimo->estado_nuevo) }}
          ({{ $ultimo->created_at->format('d/m/Y H:i:s') }})
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
