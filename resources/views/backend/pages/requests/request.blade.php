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
            <h4 class="page-title">Crear Solicitud</h4>
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
                    <form action="{{ route('admin.service.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12 col-sm-12">
                                <label for="tipo_persona">Tipo de Persona</label>
                                <select id="tipo_persona" name="tipo_persona" class="form-control select2">
                                    <option value="natural">Persona Natural</option>
                                    <option value="juridica">Persona Jurídica</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Fecha</label>
                                    <input type="date" name="fecha_registro" class="form-control" id="fecha_registro" placeholder="Fecha Registro" value="{{ old('fecha_registro') }}">
                                </div>
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Razón Social</label>
                                    <input type="text" name="razon_social" class="form-control" placeholder="Razón Social" value="{{ old('razon_social') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                </div>
                        </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Tipo de ID</label>
                                    <select name="tipo_id" id="tipo_id" class="form-control select2">
                                        <option value="cc">C.C.</option>
                                        <option value="ce">C.E.</option>
                                        <option value="pa">P.A.</option>
                                        <option value="ppt">PPT</option>
                                        <option value="pep">PEP</option>
                                    </select>
                                </div>
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Número de ID</label>
                                    <input type="text" name="identificador" placeholder="Identificador" value="{{ old('identificador') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" maxlength="50">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group  col-md-12 col-sm-12">
                                    <label>Motivo</label>
                                    <textarea name="motivo" class="form-control" placeholder="Motivo" value="{{ old('motivo') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"></textarea>
                                </div>
                            </div>
                            <div id="persona_natural" class="persona-section">
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Nombre Completo</label>
                                    <input type="text" class="form-control" placeholder="Nombre Completo" value="{{ old('nombre_completo') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                </div>
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Tipo de Visitante</label>
                                    <select name="tipo_visitante" class="form-control select2">
                                        <option value="proveedor">Proveedor</option>
                                        <option value="visitante">Visitante</option>
                                    </select>
                                </div>
                            </div>
                            </div>
                            <div id="persona_juridica" class="persona-section hidden">
                                <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Subir Archivo</label>
                                    <input type="file" name="archivo" class="form-control">
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Tipo de Cliente</label>
                                    <select name="tipo_cliente" class="form-control">
                                        <option value="proveedor">Proveedor</option>
                                        <option value="cliente">Cliente</option>
                                    </select>
                                </div>
                                </div>
                            </div>


                        <button type="submit" class="btn btn-primary">Guardar</button>
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

        tipoPersona.addEventListener("change", function() {
            if (this.value === "natural") {
                natural.classList.remove("hidden");
                juridica.classList.add("hidden");
            } else {
                juridica.classList.remove("hidden");
                natural.classList.add("hidden");
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        let tipoPersona = document.getElementById("tipo_persona");
        let natural = document.getElementById("persona_natural");
        let juridica = document.getElementById("persona_juridica");
        let tipoId = document.getElementById("tipo_id");

        const opcionesNatural = `
            <option value="cc">C.C.</option>
            <option value="ce">C.E.</option>
            <option value="pa">P.A.</option>
            <option value="ppt">PPT</option>
            <option value="pep">PEP</option>
        `;

        const opcionesJuridica = `
            <option value="nit">NIT</option>
            <option value="internacional">INTERNACIONAL</option>
        `;

        tipoPersona.addEventListener("change", function() {
            if (this.value === "natural") {
                natural.classList.remove("hidden");
                juridica.classList.add("hidden");
                tipoId.innerHTML = opcionesNatural; // Cambiar opciones del select
            } else {
                juridica.classList.remove("hidden");
                natural.classList.add("hidden");
                tipoId.innerHTML = opcionesJuridica; // Cambiar opciones del select
            }
        });

        // Para cargar la opción correcta al recargar la página con valores antiguos
        if (tipoPersona.value === "natural") {
            tipoId.innerHTML = opcionesNatural;
        } else {
            tipoId.innerHTML = opcionesJuridica;
        }
    });
</script>
@endsection
