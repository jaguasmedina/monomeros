@extends('backend.layouts.master')

@section('title', 'Reporte de Solicitudes')

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Reporte de Solicitudes</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <!-- FILTROS -->
    <form action="{{ route('admin.reports.solicitudes') }}" method="GET" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio"
                       value="{{ $filters['fecha_inicio'] ?? '' }}"
                       class="form-control">
            </div>
            <div class="form-group col-md-3">
                <label>Fecha fin</label>
                <input type="date" name="fecha_fin"
                       value="{{ $filters['fecha_fin'] ?? '' }}"
                       class="form-control">
            </div>
            <div class="form-group col-md-3">
                <label>Razón Social</label>
                <input type="text" name="razon_social"
                       value="{{ $filters['razon_social'] ?? '' }}"
                       class="form-control" placeholder="Buscar...">
            </div>
            <div class="form-group col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    @foreach($estados as $estadoOp)
                        <option value="{{ $estadoOp }}"
                            {{ ( ($filters['estado'] ?? '') === $estadoOp ) ? 'selected' : '' }}>
                            {{ ucfirst(strtolower($estadoOp)) }}
                        </option>
                    @endforeach
                </select>
            </div>       
                          
            


        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="{{ route('admin.reports.solicitudes.export', request()->all()) }}"
            class="btn btn-success btn-sm">
             <i class="fa fa-file-excel-o"></i> Descargar Excel
         </a>
        
    </form>

   

    <!-- TABLA DE RESULTADOS -->
    <div class="data-tables">
        <table id="dataTable" class="table table-bordered text-center">
            <thead class="bg-light text-capitalize">
                <tr>
                    <th>Razón Social</th>
                    <th>Fecha</th>
                    <th>Identificador</th>
                    <th>Motivo</th>
                    <th>Tipo Cliente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $sol)
                    <tr>
                        <td>{{ $sol->razon_social }}</td>
                        <td>{{ \Carbon\Carbon::parse($sol->fecha_registro)->format('d/m/Y') }}</td>
                        <td>{{ $sol->tipo_id }} {{ $sol->identificador }}</td>
                        <td>{{ $sol->motivo }}</td>
                        <td>{{ $sol->tipo_cliente }}</td>
                        <td>{{ ucfirst(strtolower($sol->estado)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay solicitudes para estos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#dataTable').DataTable({ responsive: true });
        });
    </script>
@endsection
