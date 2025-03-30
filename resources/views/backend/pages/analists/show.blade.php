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
                    <form id="solicitudForm" action="{{ route('admin.analists.save', $solicitud->id) }}" method="POST" enctype="multipart/form-data">
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
                                <div class="form-group  col-md-8 col-sm-12">
                                    <label>Motivo</label>
                                    <textarea name="motivo" readonly required  class="form-control" placeholder="Motivo" value="{{ $solicitud->motivo }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">{{ $solicitud->motivo }}</textarea>
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
                            </div>
                            <div class="form-row">
                                <div class="col-12">
                                    <button type="button" id="addMemberBtn" class="btn btn-primary btn-sm">Agregar Miembro</button>
                                </div>
                            </div>
                            <div id="membersContainer">
                                @foreach ($solicitud->miembros as $index => $miembro)
                                    <div class="form-row member" data-miembro-id= {{ $miembro->id }}>
                                        <div class="form-group col-md-3 col-sm-12">
                                            <label>Título</label>
                                            <input type="text" name="miembros[{{ $index }}][titulo]" class="form-control" value="{{ $miembro->titulo }}" required>
                                        </div>
                                        <div class="form-group col-md-3 col-sm-12">
                                            <label>Nombre</label>
                                            <input type="text" name="miembros[{{ $index }}][nombre]" class="form-control" value="{{ $miembro->nombre }}" required>
                                        </div>
                                        <div class="form-group col-md-2 col-sm-12">
                                            <label>Tipo ID</label>
                                            <select name="miembros[{{ $index }}][tipo_id]" class="form-control" required>
                                                <option value="cc" {{ $miembro->tipo_id == 'cc' ? 'selected' : '' }}>C.C.</option>
                                                <option value="ce" {{ $miembro->tipo_id == 'ce' ? 'selected' : '' }}>C.E.</option>
                                                <option value="pa" {{ $miembro->tipo_id == 'pa' ? 'selected' : '' }}>P.A.</option>
                                                <option value="ppt" {{ $miembro->tipo_id == 'ppt' ? 'selected' : '' }}>PPT</option>
                                                <option value="pep" {{ $miembro->tipo_id == 'pep' ? 'selected' : '' }}>PEP</option>
                                                <option value="nit" {{ $miembro->tipo_id == 'nit' ? 'selected' : '' }}>NIT</option>
                                                <option value="internacional" {{ $miembro->tipo_id == 'internacional' ? 'selected' : '' }}>INTERNACIONAL</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2 col-sm-12">
                                            <label>Número ID</label>
                                            <input type="text" name="miembros[{{ $index }}][numero_id]" class="form-control" value="{{ $miembro->numero_id }}" required>
                                        </div>
                                        <div class="form-group col-md-2 col-sm-12">
                                            <label>¿Favorable?</label>
                                            <select name="miembros[{{ $index }}][favorable]" class="form-control favorable-select" required>
                                                <option value="si" {{ $miembro->favorable == 'si' ? 'selected' : '' }}>Sí</option>
                                                <option value="no" {{ $miembro->favorable == 'no' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-danger btn-sm removeMemberBtn">Eliminar</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="conceptoContainer" class="form-group col-md-12 col-sm-12 {{ $solicitud->miembros->where('favorable', 'no')->count() ? '' : 'hidden' }}">
                                <label>Concepto de No Favorable</label>
                                <textarea name="concepto_no_favorable" id="concepto_no_favorable" class="form-control" placeholder="Explique el motivo">{{ $solicitud->miembros->where('favorable', 'no')->first()?->concepto_no_favorable ?? '' }}</textarea>
                            </div>
                            <br>

                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="#" onclick="showDevuelto(1)" class="btn btn-secondary">Devolver</a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                    <form id="devueltoForm" class="hidden" action="{{ route('admin.analists.savenf', $solicitud->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group  col-md-6 col-sm-12">
                            <label>Numero Solicitud</label>
                            <input type="text" readonly name="numero_solicitud" class="form-control" placeholder="Numero Solicitud" value="{{ $solicitud->id }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div id="conceptoContainer" class="form-group col-md-12 col-sm-12">
                            <label>Concepto de Devolución</label>
                            <textarea name="concepto_no_favorable" id="concepto_no_favorable" class="form-control" placeholder="Explique el motivo">{{ $solicitud->miembros->where('favorable', 'no')->first()?->concepto_no_favorable ?? '' }}</textarea>
                        </div>
                        <div class="form-group col-md-2 col-sm-12">
                            <label>Razón de devolución.</label>
                            <select name="razon_documentacion" id="razon_documentacion" class="form-control favorable-select" required>
                                <option value="documentacion">Documentación</option>
                                <option value="entregado">Otro</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="#" onclick="showDevuelto(2)" class="btn btn-secondary">Regresar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showDevuelto(id){
        if(id == 1){
            let devueltoForm = document.getElementById("devueltoForm");
            let solicitudForm = document.getElementById("solicitudForm");
            devueltoForm.classList.remove("hidden");
            solicitudForm.classList.add("hidden");
        }else{
            let devueltoForm = document.getElementById("devueltoForm");
            let solicitudForm = document.getElementById("solicitudForm");
            solicitudForm.classList.remove("hidden");
            devueltoForm.classList.add("hidden");
        }
    }
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    let maxMembers = 2;
    let memberCount = document.querySelectorAll("#membersContainer .member").length;
    let membersContainer = document.getElementById("membersContainer");
    let conceptoContainer = document.getElementById("conceptoContainer");
    let addMemberBtn = document.getElementById("addMemberBtn");

    function contarMiembros() {
        return document.querySelectorAll("#membersContainer .member").length;
    }

    function verificarLimiteMiembros() {
        addMemberBtn.disabled = contarMiembros() >= maxMembers;
    }

    addMemberBtn.addEventListener("click", function() {
        if (contarMiembros() < maxMembers) {
            memberCount++;
            let memberDiv = document.createElement("div");
            memberDiv.classList.add("form-row", "member");
            memberDiv.innerHTML = `
                <div class="form-group col-md-3 col-sm-12">
                    <label>Título</label>
                    <input type="text" name="miembros[${memberCount}][titulo]" class="form-control upper" required>
                </div>
                <div class="form-group col-md-3 col-sm-12">
                    <label>Nombre</label>
                    <input type="text" name="miembros[${memberCount}][nombre]" class="form-control upper" required>
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <label>Tipo ID</label>
                    <select name="miembros[${memberCount}][tipo_id]" class="form-control" required>
                        <option value="cc">C.C.</option>
                        <option value="ce">C.E.</option>
                        <option value="pa">P.A.</option>
                        <option value="ppt">PPT</option>
                        <option value="pep">PEP</option>
                        <option value="nit">NIT</option>
                        <option value="internacional">INTERNACIONAL</option>
                    </select>
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <label>Número ID</label>
                    <input type="text" name="miembros[${memberCount}][numero_id]" class="form-control" required>
                </div>
                <div class="form-group col-md-2 col-sm-12">
                    <label>¿Favorable?</label>
                    <select name="miembros[${memberCount}][favorable]" class="form-control favorable-select" required>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <button type="button" class="btn btn-danger btn-sm removeMemberBtn">Eliminar</button>
                </div>
            `;

            membersContainer.appendChild(memberDiv);
            verificarLimiteMiembros();
            actualizarEventosFavorable();
        }
    });

    membersContainer.addEventListener("click", function(event) {
        if (event.target.classList.contains("removeMemberBtn")) {
            event.target.closest(".member").remove();
            verificarLimiteMiembros();
            actualizarEventosFavorable();
        }
    });

    function actualizarEventosFavorable() {
        document.querySelectorAll(".favorable-select").forEach(select => {
            select.addEventListener("change", function() {
                let hayNoFavorable = Array.from(document.querySelectorAll(".favorable-select")).some(s => s.value === "no");
                conceptoContainer.classList.toggle("hidden", !hayNoFavorable);
            });
        });
    }

    actualizarEventosFavorable();

    document.querySelectorAll(".upper").forEach(input => {
        input.addEventListener("input", function() {
            this.value = this.value.toUpperCase();
        });
    });

    verificarLimiteMiembros();
});

</script>
@endsection
