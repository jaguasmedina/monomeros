@extends('backend.layouts.master')

@section('title', 'Consultar Solicitud')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .hidden { display: none; }
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
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Consultar Solicitud</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Consultar Solicitud</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.service.query') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Número Solicitud</label>
                                <input type="text" name="numero_solicitud" class="form-control" 
                                       value="{{ old('numero_solicitud', $numero_solicitud ?? '') }}"
                                       style="text-transform: uppercase;">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Número de ID</label>
                                <input type="text" name="identificador" class="form-control" 
                                       value="{{ old('identificador', $identificador ?? '') }}"
                                       style="text-transform: uppercase;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Consultar</button>
                        <a href="{{ route('admin.service.query') }}" class="btn btn-secondary">Limpiar</a>
                    </form>

                    @php
                        $showAcciones = false;
                        if(isset($solicitud) && $solicitud->isNotEmpty()) {
                            foreach ($solicitud as $solicitu) {
                                if ((auth()->user()->can('admin.edit') && strtolower($solicitu->estado) == 'documentacion') ||
                                    (auth()->user()->can('admin.edit') && strtolower($solicitu->estado) == 'entregado')) {
                                    $showAcciones = true;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @if(isset($solicitud) && $solicitud->isNotEmpty())
                        <div class="mt-4">
                            <h5>Resultados de la Consulta @if(isset($identificador)): {{ strtoupper($identificador) }} @endif</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID SOLICITUD</th>
                                            <th>RAZÓN SOCIAL</th>
                                            <th>TIPO ID</th>
                                            <th>IDENTIFICADOR</th>
                                            <th>FECHA REGISTRO</th>
                                            <th>MOTIVO</th>
                                            <th>ESTADO</th>
                                            <th>CONCEPTO</th>
                                            @if($showAcciones)
                                                <th>ACCIONES</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($solicitud as $solicitu)
                                            <tr>
                                                <td>{{ strtoupper($solicitu->id) }}</td>
                                                <td>{{ strtoupper($solicitu->razon_social) }}</td>
                                                <td>{{ strtoupper($solicitu->tipo_id) }}</td>
                                                <td>{{ strtoupper($solicitu->identificador) }}</td>
                                                <td>{{ strtoupper($solicitu->fecha_registro) }}</td>
                                                <td>{{ strtoupper($solicitu->motivo) }}</td>
                                                <td>{{ strtoupper($solicitu->estado) }}</td>
                                                <td>{{ strtoupper($solicitu->concepto) }}</td>
                                                @if($showAcciones)
                                                    <td>
                                                        @if (auth()->user()->can('admin.edit') && strtolower($solicitu->estado) == 'documentacion')
                                                            <a class="btn btn-success text-white" href="{{ route('admin.service.edit', $solicitu->id) }}">Editar</a>
                                                        @elseif (auth()->user()->can('admin.edit') && strtolower($solicitu->estado) == 'entregado')
                                                            <a class="btn btn-success text-white" href="{{ route('admin.service.documento.final', $solicitu->id) }}">Descargar</a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            
                                            @php
                                                $colspan = $showAcciones ? 9 : 8;
                                            @endphp

                                            {{-- Mostrar el flujo de estado según corresponda --}}
                                            @include('backend.pages.requests.partials.estado_flow', ['estado' => $solicitu->estado, 'colspan' => $colspan])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif(request()->isMethod('post'))
                        <div class="alert alert-warning mt-4">
                            No se encontraron solicitudes con los datos proporcionados.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Convertir a mayúsculas automáticamente
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    });
</script>
@endsection