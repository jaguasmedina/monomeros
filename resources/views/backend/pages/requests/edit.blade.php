@extends('backend.layouts.master')

@section('title', 'Crear Solicitud')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .hidden { display: none; }
</style>
@endsection

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title">Editar Solicitud</h4>
        </div>
    </div>
</div>
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Nueva Solicitud</h4>
                    @include('backend.layouts.partials.messages')
                    @if (session('solicitud_id'))
                        <div class="alert alert-info">
                            ID de la solicitud creada: <h1></h1><strong>{!! session('solicitud_id') !!}</strong></h1>
                        </div>
                    @endif
                    <form action="{{ route('admin.service.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12 col-sm-12">
                                <label for="tipo_persona">Tipo de Persona</label>
                                <select id="tipo_persona" name="tipo_persona" class="form-control select2">
                                    <option value="natural" @selected($solicitud->tipo_persona == 'natural')>Persona Natural</option>
                                    <option value="juridica" @selected($solicitud->tipo_persona == 'juridica')>Persona Jurídica</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id" value="{{ $solicitud->id }}">
                        <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Fecha</label>
                                    <input type="date" name="fecha_registro" class="form-control" id="fecha_registro" 
       value="{{ old('fecha_registro', $solicitud->fecha_registro) }}" 
       required max="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Razón Social</label>
                                    <input type="text" required  name="razon_social" id="razon_social" class="form-control" placeholder="Razón Social" value="{{ $solicitud->razon_social }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                </div>
                        </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Tipo de ID</label>
                                    <select name="tipo_id" id="tipo_id" class="form-control select2">
                                        <option value="cc" @selected($solicitud->tipo_documento == 'cc')>C.C.</option>
                                        <option value="ce" @selected($solicitud->tipo_documento == 'ce')>C.E.</option>
                                        <option value="pa" @selected($solicitud->tipo_documento == 'pa')>P.A.</option>
                                        <option value="ppt" @selected($solicitud->tipo_documento == 'ppt')>PPT</option>
                                        <option value="pep" @selected($solicitud->tipo_documento == 'pep')>PEP</option>
                                        <option value="ti" @selected($solicitud->tipo_documento == 'ti')>TI</option>
                                        <option value="rc" @selected($solicitud->tipo_documento == 'rc')>RC</option>
                                    </select>
                                </div>
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Número de ID</label>
                                    <input type="text" required  name="identificador" placeholder="Identificador" value="{{ $solicitud->identificador }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" maxlength="50">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group  col-md-12 col-sm-12">
                                    <label>Motivo</label>
                                    <textarea name="motivo" required  class="form-control" placeholder="Motivo" value="{{  $solicitud->motivo  }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">{{  $solicitud->motivo  }}</textarea>
                                </div>
                            </div>

                            <div class="form-row">

                                    <div class="form-group col-md-6 col-sm-12" id="persona_natural">
                                        <label>Nombre Completo</label>
                                        <input type="text" class="form-control" name="nombre_completo" id="nombre_completo" placeholder="Nombre Completo" value="{{  $solicitud->nombre_completo  }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>Descargar Documento</label>
                                        @if($solicitud->archivo)
                                            <a href="{{ asset('storage/' . $solicitud->archivo) }}" class="btn btn-success" target="_blank">
                                                <i class="fa fa-download"></i> Descargar PDF
                                            </a>
                                        @else
                                            <p>No hay documento adjunto</p>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12 hidden" id="persona_juridica" class="persona-section ">
                                        <label>Subir Archivo</label>
                                        <input type="file" name="archivo" class="form-control">
                                    </div>

                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Tipo</label>
                                    <select name="tipo_cliente" id="tipo_cliente" class="form-control select2">
                                        <option value="contratista" @selected($solicitud->tipo_cliente == 'contratista')>Contratista</option>
                                        <option value="visitante" @selected($solicitud->tipo_cliente == 'visitante')>Visitante</option>
                                    </select>
                                </div>
                            </div>

                        <button type="submit" class="btn btn-primary">Guardar cambio</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let tipoPersona = document.getElementById("tipo_persona");
        let natural = document.getElementById("persona_natural");
        let juridica = document.getElementById("persona_juridica");
        let tipoId = document.getElementById("tipo_id");
        let tipoCliente = document.getElementById("tipo_cliente");

        const opcionesNatural = `
            <option value="cc">C.C.</option>
            <option value="ce">C.E.</option>
            <option value="pa">P.A.</option>
            <option value="ppt">PPT</option>
            <option value="pep">PEP</option>
            <option value="ti">TI</option>
            <option value="rc">RC</option>
        `;

        const opcionesJuridica = `
            <option value="nit">NIT</option>
            <option value="internacional">INTERNACIONAL</option>
        `;

        const opcionesNaturalTipo = `
            <option value="contratista">Contratista</option>
            <option value="visitante">Visitante</option>
        `;

        const opcionesJuridicaTipo = `
            <option value="cliente">Cliente</option>
            <option value="proveedor">Proveedor</option>
        `;

        function actualizarVisibilidad() {
            if (tipoPersona.value === "natural") {
                natural.classList.remove("hidden");
                juridica.classList.add("hidden");
                tipoId.innerHTML = opcionesNatural;
                tipoCliente.innerHTML = opcionesNaturalTipo;
            } else {
                juridica.classList.remove("hidden");
                natural.classList.add("hidden");
                tipoId.innerHTML = opcionesJuridica;
                tipoCliente.innerHTML = opcionesJuridicaTipo;
            }
        }

        // Llamar a la función para establecer los valores correctos al cargar la página
        actualizarVisibilidad();

        // Evento para cambiar la visibilidad y opciones cuando el usuario selecciona un tipo de persona
        tipoPersona.addEventListener("change", actualizarVisibilidad);
    });
</script>
@endsection

