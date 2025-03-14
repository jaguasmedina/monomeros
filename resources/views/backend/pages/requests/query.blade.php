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
                    <form action="{{ route('admin.service.queryreq') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Numero Solicitud</label>
                                    <input type="text" name="numero_solicitud" class="form-control" placeholder="Numero Solicitud" value="{{ old('numero_solicitud') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                </div>
                                <div class="form-group  col-md-6 col-sm-12">
                                    <label>Número de ID</label>
                                    <input type="text" name="identificador" placeholder="Identificador" value="{{ old('identificador') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" maxlength="50">
                                </div>

                        </div>
                        <button type="submit" class="btn btn-primary">Consultar</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                    <!-- Mostrar resultados si existen solicitudes -->
                    @if(isset($solicitud) && $solicitud->isNotEmpty())
                        <div class="mt-4">
                            <h5>Resultados de la Consulta : {{ $identificador }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID Solicitud</th>
                                            <th>Razón Social</th>
                                            <th>Motivo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($solicitud as $solicitu)
                                            <tr>
                                                <td>{{ $solicitu->id }}</td>
                                                <td>{{ $solicitu->razon_social }}</td>
                                                <td>{{ $solicitu->motivo }}</td>
                                                <td>{{ $solicitu->estado }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif(isset($numero_solicitud) || isset($identificador))
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
                tipoId.innerHTML = opcionesNatural;
            } else {
                juridica.classList.remove("hidden");
                natural.classList.add("hidden");
                tipoId.innerHTML = opcionesJuridica;
            }
        });

        if (tipoPersona.value === "natural") {
            tipoId.innerHTML = opcionesNatural;
        } else {
            tipoId.innerHTML = opcionesJuridica;
        }
    });
</script>
@endsection
