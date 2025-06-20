@extends('backend.layouts.master')

@section('title', 'Consultar Solicitud')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .hidden { display: none; }
</style>
@endsection

@section('admin-content')
<div class="page-title-area">
    <h4 class="page-title">Consultar Solicitud</h4>
</div>

<div class="main-content-inner">
    <div class="card mt-4">
        <div class="card-body">
            @include('backend.layouts.partials.messages')

            {{-- FORMULARIO PRINCIPAL --}}
            <form id="solicitudForm"
                  action="{{ route('admin.analists.save', $solicitud->id) }}"
                  method="POST">
                @csrf

                {{-- Encabezado de datos --}}
                <div class="form-row mb-3">
                    <div class="form-group col-md-6">
                        <label>Número Solicitud</label>
                        <input type="text" readonly name="numero_solicitud"
                               value="{{ $solicitud->id }}"
                               class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Fecha</label>
                        <input type="date" readonly name="fecha_registro"
                               value="{{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('Y-m-d') }}"
                               class="form-control">
                    </div>
                </div>

                {{-- Datos de cliente --}}
                <div class="form-row mb-3">
                    <div class="form-group col-md-6">
                        <label>Razón Social</label>
                        <input type="text" readonly
                               value="{{ $solicitud->razon_social }}"
                               class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Identificación</label>
                        <input type="text" readonly
                               value="{{ $solicitud->tipo_id }} {{ $solicitud->identificador }}"
                               class="form-control">
                    </div>
                </div>

                {{-- Motivo --}}
                <div class="form-row mb-3">
                    <div class="form-group col-md-12">
                        <label>Motivo</label>
                        <textarea readonly class="form-control">{{ $solicitud->motivo }}</textarea>
                    </div>
                </div>

                {{-- Descargar documento(s) --}}
                <div class="form-row mb-3">
                    <div class="form-group col-md-12">
                        <label>Descargar Documento</label><br>
                        @php
                            $paths = [];
                            if ($solicitud->archivo) {
                                $decoded = json_decode($solicitud->archivo,true);
                                $paths   = is_array($decoded) ? $decoded : [$solicitud->archivo];
                            }
                        @endphp
                        @if(count($paths))
                            @foreach($paths as $path)
                                <a href="{{ asset('storage/' . trim($path,'"')) }}"
                                   class="btn btn-success btn-sm mb-1" target="_blank">
                                    <i class="fa fa-download"></i>
                                    {{ basename($path) }}
                                </a>
                            @endforeach
                        @else
                            <p>No hay documento adjunto</p>
                        @endif
                    </div>
                </div>

                {{-- Botón Agregar Miembro --}}
                <div class="form-row mb-3">
                    <div class="col-12">
                        <button type="button" id="addMemberBtn"
                                class="btn btn-primary btn-sm">
                            Agregar Miembro
                        </button>
                    </div>
                </div>

                {{-- Contenedor dinámico de miembros --}}
                <div id="membersContainer">
                    @foreach($solicitud->miembros as $index => $miembro)
                        <div class="form-row member mb-2" data-miembro-id="{{ $miembro->id }}">
                            <div class="form-group col-md-3">
                                <label>TÍTULO</label>
                                <input type="text" name="miembros[{{ $index }}][titulo]"
                                       value="{{ $miembro->titulo }}"
                                       class="form-control" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>NOMBRE</label>
                                <input type="text" name="miembros[{{ $index }}][nombre]"
                                       value="{{ $miembro->nombre }}"
                                       class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>TIPO ID</label>
                                <select name="miembros[{{ $index }}][tipo_id]"
                                        class="form-control" required>
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
                                <label>NÚMERO ID</label>
                                <input type="text" name="miembros[{{ $index }}][numero_id]"
                                       value="{{ $miembro->numero_id }}"
                                       class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>¿FAVORABLE?</label>
                                <select name="miembros[{{ $index }}][favorable]"
                                        class="form-control favorable-select" required>
                                    <option value="si" {{ $miembro->favorable=='si'?'selected':'' }}>Sí</option>
                                    <option value="no" {{ $miembro->favorable=='no'?'selected':'' }}>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>OBSERVACIONES</label>
                                <textarea name="miembros[{{ $index }}][observaciones]"
                                          class="form-control">{{ $miembro->observaciones }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="button" class="btn btn-danger btn-sm removeMemberBtn">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Campo Observaciones al devolver --}}
                <div id="motivoRechazoContainer" class="form-group mt-3 hidden">
                    <label for="motivo_rechazo">Observaciones (motivo de devolución) *</label>
                    <textarea name="motivo_rechazo"
                              id="motivo_rechazo"
                              class="form-control"
                              rows="3"
                              required>{{ old('motivo_rechazo', $solicitud->motivo_rechazo ?? '') }}</textarea>
                </div>

                {{-- Campo oculto que define la acción --}}
                <input type="hidden" name="accion" id="accion" value="procesar">

                {{-- Botones de acción unificados --}}
                <div class="form-row mt-3">
                    <div class="col">
                        <button type="submit" class="btn btn-primary"
                                onclick="hideMotivo(); document.getElementById('accion').value='procesar'">
                            PROCESAR
                        </button>

                        <button type="submit" class="btn btn-warning"
                                onclick="
                                    if (document.getElementById('motivoRechazoContainer').classList.contains('hidden')) {
                                        showMotivo();
                                        return false;
                                    }
                                    document.getElementById('accion').value='documentacion';
                                ">
                            Regresar por Documentación
                        </button>

                        <button type="submit" class="btn btn-info"
                                onclick="hideMotivo(); document.getElementById('accion').value='borrador'">
                            Guardar en Borrador
                        </button>

                        <a href="{{ route('admin.analists.index') }}"
                           class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Control motivo devolución
    const motivoContainer = document.getElementById('motivoRechazoContainer');
    const motivoTextarea  = document.getElementById('motivo_rechazo');

    window.showMotivo = function() {
        motivoContainer.classList.remove('hidden');
        motivoTextarea.required = true;
    };
    window.hideMotivo = function() {
        motivoContainer.classList.add('hidden');
        motivoTextarea.required = false;
    };

    // Al cargar, ocultamos el textarea
    hideMotivo();

    // --- Miembros dinámicos ---
    const maxMembers = 100;
    let memberCount = document.querySelectorAll("#membersContainer .member").length;
    const container   = document.getElementById("membersContainer");
    const addBtn      = document.getElementById("addMemberBtn");

    function updateButtons() {
        addBtn.disabled = container.querySelectorAll(".member").length >= maxMembers;
    }

    function bindFavorable() {
        document.querySelectorAll(".favorable-select").forEach(sel => {
            sel.addEventListener("change", () => {
                // aquí podrías reaccionar a cambios “no”
            });
        });
    }

    function addMember() {
        if (container.querySelectorAll(".member").length >= maxMembers) return;
        memberCount++;
        const div = document.createElement("div");
        div.className = "form-row member mb-2";
        div.innerHTML = `
            <div class="form-group col-md-3">
                <label>TÍTULO</label>
                <input type="text" name="miembros[${memberCount}][titulo]" class="form-control" required>
            </div>
            <div class="form-group col-md-3">
                <label>NOMBRE</label>
                <input type="text" name="miembros[${memberCount}][nombre]" class="form-control" required>
            </div>
            <div class="form-group col-md-2">
                <label>TIPO ID</label>
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
            <div class="form-group col-md-2">
                <label>NÚMERO ID</label>
                <input type="text" name="miembros[${memberCount}][numero_id]" class="form-control" required>
            </div>
            <div class="form-group col-md-2">
                <label>¿FAVORABLE?</label>
                <select name="miembros[${memberCount}][favorable]" class="form-control favorable-select" required>
                    <option value="si">Sí</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>OBSERVACIONES</label>
                <textarea name="miembros[${memberCount}][observaciones]" class="form-control"></textarea>
            </div>
            <div class="form-group col-md-12">
                <button type="button" class="btn btn-danger btn-sm removeMemberBtn">
                    Eliminar
                </button>
            </div>
        `;
        container.appendChild(div);
        bindFavorable();
        updateButtons();
    }

    addBtn.addEventListener("click", addMember);

    container.addEventListener("click", e => {
        if (e.target.classList.contains("removeMemberBtn")) {
            e.target.closest(".member").remove();
            bindFavorable();
            updateButtons();
        }
    });

    bindFavorable();
    updateButtons();
});
</script>
@endsection
