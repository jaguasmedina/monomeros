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
                    <form id="solicitudForm" action="{{ route('admin.analists.save', $solicitud->id) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Número Solicitud</label>
                                <input type="text" readonly name="numero_solicitud" value="{{ $solicitud->id }}" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Fecha</label>
                                <input type="date" readonly name="fecha_registro" value="{{ $solicitud->fecha_registro }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Razón Social</label>
                                <input type="text" readonly value="{{ $solicitud->razon_social }}" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Identificación</label>
                                <input type="text" readonly value="{{ $solicitud->tipo_id }} {{ $solicitud->identificador }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>Motivo</label>
                                <textarea readonly class="form-control">{{ $solicitud->motivo }}</textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Descargar Documento</label><br>
                                @php
                                // Si el campo viene como JSON, lo decodificamos. Si no, lo tratamos como string único.
                                $paths = [];
                                if ($solicitud->archivo) {
                                    $decoded = json_decode($solicitud->archivo, true);
                                    $paths   = is_array($decoded) ? $decoded : [$solicitud->archivo];
                                }
                            @endphp
                            
                            @if(count($paths))
                                <label>Descargar Documento Original</label><br>
                                @foreach($paths as $path)
                                    <a href="{{ asset('storage/' . trim($path, '"')) }}"
                                       class="btn btn-success btn-sm"
                                       target="_blank">
                                        <i class="fa fa-download"></i> {{ basename($path) }}
                                    </a>
                                @endforeach
                            @else
                                <p>No hay documento adjunto</p>
                            @endif
                            
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-12">
                                <button type="button" id="addMemberBtn" class="btn btn-primary btn-sm">Agregar Miembro</button>
                            </div>
                        </div>
                        <div id="membersContainer">
                            @foreach($solicitud->miembros as $index => $miembro)
                                <div class="form-row member" data-miembro-id="{{ $miembro->id }}">
                                    <div class="form-group col-md-3">
                                        <label>Título</label>
                                        <input type="text" name="miembros[{{ $index }}][titulo]" value="{{ $miembro->titulo }}" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Nombre</label>
                                        <input type="text" name="miembros[{{ $index }}][nombre]" value="{{ $miembro->nombre }}" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Tipo ID</label>
                                        <select name="miembros[{{ $index }}][tipo_id]" class="form-control" required>
                                            <option value="cc" {{ $miembro->tipo_id=='cc'?'selected':'' }}>C.C.</option>
                                            <option value="ce" {{ $miembro->tipo_id=='ce'?'selected':'' }}>C.E.</option>
                                            <option value="pa" {{ $miembro->tipo_id=='pa'?'selected':'' }}>P.A.</option>
                                            <option value="ppt" {{ $miembro->tipo_id=='ppt'?'selected':'' }}>PPT</option>
                                            <option value="pep" {{ $miembro->tipo_id=='pep'?'selected':'' }}>PEP</option>
                                            <option value="ti" {{ $miembro->tipo_id=='ti'?'selected':'' }}>TI</option>
                                            <option value="rc" {{ $miembro->tipo_id=='rc'?'selected':'' }}>RC</option>
                                            <option value="nit" {{ $miembro->tipo_id=='nit'?'selected':'' }}>NIT</option>
                                            <option value="internacional" {{ $miembro->tipo_id=='internacional'?'selected':'' }}>INTERNACIONAL</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Número ID</label>
                                        <input type="text" name="miembros[{{ $index }}][numero_id]" value="{{ $miembro->numero_id }}" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>¿Favorable?</label>
                                        <select name="miembros[{{ $index }}][favorable]" class="form-control favorable-select" required>
                                            <option value="si" {{ $miembro->favorable=='si'?'selected':'' }}>Sí</option>
                                            <option value="no" {{ $miembro->favorable=='no'?'selected':'' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Observaciones</label>
                                        <textarea name="miembros[{{ $index }}][observaciones]" class="form-control">{{ $miembro->observaciones ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-danger btn-sm removeMemberBtn">Eliminar</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="conceptoContainer" class="form-group col-md-12 {{ $solicitud->miembros->contains(fn($m)=>$m->favorable=='no')?'':'hidden' }}">
                            <label>Concepto de No Favorable</label>
                            <textarea name="concepto_no_favorable" class="form-control">{{ $solicitud->miembros->where('favorable','no')->first()?->concepto_no_favorable }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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
            <option value="ti">TI</option>
            <option value="rc">RC</option>
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
                        <option value="ti">TI</option>
                        <option value="rc">RC</option>
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
                
                <div class="form-group col-md-4">
                <label>Observaciones</label>
                <textarea name="miembros[${memberCount}][observaciones]" class="form-control upper"></textarea>
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