@extends('backend.layouts.master')

@section('title', 'Consultar Solicitud')

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
                    <form action="{{ route('admin.analists.index') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Numero Solicitud</label>
                                    <input type="text" readonly name="numero_solicitud" class="form-control" placeholder="Numero Solicitud" value="{{ $solicitud->id }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Fecha</label>
                                    <input type="date" readonly name="fecha_registro" class="form-control" id="fecha_registro" placeholder="Fecha Registro" value="{{ $solicitud->fecha_registro }}">
                                </div>
                        </div>
                            <div class="form-row">
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Razón Social</label>
                                    <input type="text" readonly name="identificador" placeholder="Identificador" value="{{ $solicitud->razon_social }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" maxlength="50">
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>Identificación</label>
                                    <input type="text" readonly class="form-control" placeholder="Nombre Completo" value="{{ $solicitud->tipo_id  }}  {{ $solicitud->identificador }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group  col-md-12 col-sm-12">
                                    <label>Motivo</label>
                                    <textarea name="motivo" readonly required  class="form-control" placeholder="Motivo" value="{{ $solicitud->motivo }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();"></textarea>
                                </div>
                            </div>

                        <button type="submit" class="btn btn-primary">Buscar</button>
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
