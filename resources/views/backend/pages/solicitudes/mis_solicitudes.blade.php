@extends('backend.layouts.master')

@section('title', 'Mis Solicitudes')

@section('styles')
<!-- Estilos adicionales para la tabla y el flujo visual -->
<style>
    .table-responsive {
        margin-top: 20px;
    }
    /* Estilos para el flujo visual, similares a los que se usan en query.blade */
    .flow-container {
        display: flex;
        width: 600px;
        margin: 20px auto;
        font-family: Arial, sans-serif;
        position: relative;
    }
    .flow-line {
        position: absolute;
        height: 4px;
        background-color: #E0E0E0;
        top: 20px;
        left: 50px;
        right: 50px;
        z-index: 1;
    }
    .flow-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 25%;
        position: relative;
        z-index: 2;
    }
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        font-weight: bold;
        color: white;
    }
    .step-icon.active {
        background-color: #00B14F;
    }
    .step-icon.inactive {
        background-color: #9E9E9E;
    }
    .step-name {
        font-weight: bold;
        font-size: 14px;
        text-align: center;
        margin-bottom: 5px;
    }
    .step-detail {
        font-size: 12px;
        text-align: center;
        color: #666;
        min-height: 20px;
    }
    .active-text {
        color: #00B14F;
        font-weight: bold;
    }
</style>
@endsection

@section('admin-content')
@php
    // Obtenemos el usuario autenticado (del guard "admin")
    $user = auth()->guard('admin')->user();
@endphp

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Mis Solicitudes</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <!-- Listado de solicitudes -->
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Listado de Mis Solicitudes</h4>
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitudes as $solicitud)
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
                                        <td>
                                            @if(strtoupper($solicitud->estado) == 'DOCUMENTACION' && $solicitud->admin_id == auth()->guard('admin')->id())
                                                <a href="{{ route('admin.service.edit', $solicitud->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                            @elseif(strtoupper($solicitud->estado) == 'ENTREGADO')
                                                <a href="{{ route('admin.service.documento.final', $solicitud->id) }}" class="btn btn-success btn-sm" target="_blank">Descargar</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        
                                    </tr>
                                    @php
                                        // Ajusta el colspan del flujo visual: si la tabla tiene 11 columnas, usamos 11
                                        $colspan = 11;
                                    @endphp
                                    @include('backend.pages.requests.partials.estado_flow', ['estado' => $solicitud->estado, 'colspan' => $colspan])
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No se encontraron solicitudes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
