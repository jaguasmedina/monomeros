@extends('backend.layouts.master')

@section('title', 'Editar Solicitud')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('admin-content')
<div class="page-title-area">
    <h4 class="page-title">Editar Solicitud</h4>
</div>
<div class="main-content-inner">
    <div class="card mt-4">
        <div class="card-body">
            @include('backend.layouts.partials.messages')

            {{-- Mostrar motivo de devolución si existe --}}
            @if($solicitud->motivo_rechazo)
                <div class="alert alert-warning">
                    <strong>Motivo de devolución:</strong>
                    <p>{{ $solicitud->motivo_rechazo }}</p>
                </div>
            @endif

            {{-- Nuevo campo: Razón Devolución (editable o readonly según necesidad) --}}
            @if($solicitud->motivo_rechazo)
                <div class="form-group">
                    <label for="razon_devolucion">Razón Devolución</label>
                    <textarea id="razon_devolucion"
                              name="razon_devolucion"
                              class="form-control"
                              readonly
                              rows="3">{{ $solicitud->motivo_rechazo }}</textarea>
                </div>
            @endif

            <form action="{{ route('admin.service.update') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $solicitud->id }}">

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Tipo de Persona *</label>
                        <select name="tipo_persona" class="form-control select2" required>
                            <option value="natural"
                                {{ $solicitud->tipo_persona == 'natural' ? 'selected' : '' }}>
                                Persona Natural
                            </option>
                            <option value="juridica"
                                {{ $solicitud->tipo_persona == 'juridica' ? 'selected' : '' }}>
                                Persona Jurídica
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Fecha *</label>
                        <input type="date" 
                               name="fecha_registro" 
                               class="form-control"
                               value="{{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('Y-m-d') }}" 
                               required
                               readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Raz&oacute;n Social *</label>
                        <input type="text" 
                               name="razon_social" 
                               class="form-control" 
                               required
                               value="{{ $solicitud->razon_social }}"
                               style="text-transform:uppercase"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tipo de ID *</label>
                        <select name="tipo_id" class="form-control select2" required>
                            <option value="cc"           {{ $solicitud->tipo_id == 'cc'           ? 'selected' : '' }}>C.C.</option>
                            <option value="ce"           {{ $solicitud->tipo_id == 'ce'           ? 'selected' : '' }}>C.E.</option>
                            <option value="pa"           {{ $solicitud->tipo_id == 'pa'           ? 'selected' : '' }}>P.A.</option>
                            <option value="ppt"          {{ $solicitud->tipo_id == 'ppt'          ? 'selected' : '' }}>PPT</option>
                            <option value="pep"          {{ $solicitud->tipo_id == 'pep'          ? 'selected' : '' }}>PEP</option>
                            <option value="ti"           {{ $solicitud->tipo_id == 'ti'           ? 'selected' : '' }}>TI</option>
                            <option value="rc"           {{ $solicitud->tipo_id == 'rc'           ? 'selected' : '' }}>RC</option>
                            <option value="nit"          {{ $solicitud->tipo_id == 'nit'          ? 'selected' : '' }}>NIT</option>
                            <option value="internacional"{{ $solicitud->tipo_id == 'internacional'? 'selected' : '' }}>INTERNACIONAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>N&uacute;mero ID *</label>
                        <input type="text" 
                               name="identificador" 
                               class="form-control" 
                               required
                               value="{{ $solicitud->identificador }}"
                               maxlength="50"
                               style="text-transform:uppercase"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>

                <div class="form-group">
                    <label>Motivo *</label>
                    <textarea name="motivo" 
                              class="form-control" 
                              required
                              style="text-transform:uppercase"
                              oninput="this.value = this.value.toUpperCase()">{{ $solicitud->motivo }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6" id="persona_natural">
                        <label>Nombre Completo *</label>
                        <input type="text" 
                               name="nombre_completo" 
                               class="form-control"
                               value="{{ $solicitud->nombre_completo }}"
                               style="text-transform:uppercase"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group col-md-6 hidden" id="persona_juridica">
                        <label>Subir Archivos PDF *</label>
                        <input type="file" 
                               name="archivos[]" 
                               id="archivos"
                               class="form-control" 
                               multiple 
                               accept="application/pdf">

                        <div id="filePreview" class="mt-2">
                            @php
                                $archivos = json_decode($solicitud->archivo, true);
                            @endphp
                            @if(is_array($archivos))
                                @foreach($archivos as $file)
                                    <div class="file-item">
                                        <a href="{{ asset('storage/' . $file) }}"
                                           target="_blank">{{ basename($file) }}</a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tipo de Cliente *</label>
                    <select name="tipo_cliente" class="form-control select2" required>
                        <option value="contratista" {{ $solicitud->tipo_cliente=='contratista' ? 'selected' : '' }}>Contratista</option>
                        <option value="visitante"    {{ $solicitud->tipo_cliente=='visitante'    ? 'selected' : '' }}>Visitante</option>
                        <option value="cliente"      {{ $solicitud->tipo_cliente=='cliente'      ? 'selected' : '' }}>Cliente</option>
                        <option value="proveedor"    {{ $solicitud->tipo_cliente=='proveedor'    ? 'selected' : '' }}>Proveedor</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Guardar cambio</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const tipoPersona = document.querySelector('[name="tipo_persona"]');
    const nat         = document.getElementById("persona_natural");
    const jur         = document.getElementById("persona_juridica");
    const archivos    = document.getElementById("archivos");
    const preview     = document.getElementById("filePreview");

    function togglePersona() {
        if (tipoPersona.value === "juridica") {
            jur.classList.remove("hidden");
            nat.classList.add("hidden");
        } else {
            nat.classList.remove("hidden");
            jur.classList.add("hidden");
        }
    }

    tipoPersona.addEventListener("change", togglePersona);
    togglePersona();

    archivos?.addEventListener("change", function() {
        preview.innerHTML = "";
        Array.from(this.files).forEach(f => {
            const div = document.createElement("div");
            div.textContent = `${f.name} (${(f.size/1024).toFixed(1)} KB)`;
            preview.appendChild(div);
        });
    });

    $('.select2').select2();
});
</script>
@endsection
