@extends('backend.layouts.master')

@section('title', 'Crear Solicitud')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .hidden { display: none; }
    .file-input-container {
        margin-bottom: 15px;
    }
    .file-preview {
        margin-top: 5px;
    }
    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        margin-bottom: 5px;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .file-item button {
        padding: 2px 8px;
    }
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
                    @if (session('solicitud_id'))
                        <div class="alert alert-info">
                            ID de la solicitud creada: <strong>{!! session('solicitud_id') !!}</strong>
                        </div>
                    @endif
                    <form action="{{ route('admin.service.store') }}" method="POST" enctype="multipart/form-data" id="solicitudForm">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12 col-sm-12">
                                <label for="tipo_persona">Tipo de Persona *</label>
                                <select id="tipo_persona" name="tipo_persona" class="form-control select2" required>
                                    <option value="natural" {{ old('tipo_persona') == 'natural' ? 'selected' : '' }}>Persona Natural</option>
                                    <option value="juridica" {{ old('tipo_persona') == 'juridica' ? 'selected' : '' }}>Persona Jurídica</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Fecha *</label>
                                <input type="date" name="fecha_registro" class="form-control" id="fecha_registro" value="{{ old('fecha_registro', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Razón Social *</label>
                                <input type="text" required name="razon_social" id="razon_social" class="form-control" placeholder="Razón Social" value="{{ old('razon_social') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Tipo de ID *</label>
                                <select name="tipo_id" id="tipo_id" class="form-control select2" required>
                                    <option value="cc" {{ old('tipo_id') == 'cc' ? 'selected' : '' }}>C.C.</option>
                                    <option value="ce" {{ old('tipo_id') == 'ce' ? 'selected' : '' }}>C.E.</option>
                                    <option value="pa" {{ old('tipo_id') == 'pa' ? 'selected' : '' }}>P.A.</option>
                                    <option value="ppt" {{ old('tipo_id') == 'ppt' ? 'selected' : '' }}>PPT</option>
                                    <option value="pep" {{ old('tipo_id') == 'pep' ? 'selected' : '' }}>PEP</option>
                                    <option value="nit" {{ old('tipo_id') == 'nit' ? 'selected' : '' }}>NIT</option>
                                    <option value="internacional" {{ old('tipo_id') == 'internacional' ? 'selected' : '' }}>INTERNACIONAL</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Número de ID *</label>
                                <input type="text" required name="identificador" id="identificador" placeholder="Identificador" value="{{ old('identificador') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" maxlength="50">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12 col-sm-12">
                                <label>Motivo *</label>
                                <textarea name="motivo" required class="form-control" placeholder="Motivo" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">{{ old('motivo') }}</textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12" id="persona_natural">
                                <label>Nombre Completo *</label>
                                <input type="text" class="form-control" name="nombre_completo" id="nombre_completo" placeholder="Nombre Completo" value="{{ old('nombre_completo') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group col-md-6 col-sm-12 hidden" id="persona_juridica">
                                <label>Subir Archivo(s) (PDF) * <small>(Máximo 3 archivos, 2MB cada uno)</small></label>
                                <input type="file" name="archivos[]" id="archivos" class="form-control" accept="application/pdf" multiple>
                                <div id="filePreview" class="file-preview"></div>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label>Tipo*</label>
                                <select name="tipo_cliente" id="tipo_cliente" class="form-control select2" required>
                                    <option value="contratista" {{ old('tipo_cliente') == 'contratista' ? 'selected' : '' }}>Contratista</option>
                                    <option value="visitante" {{ old('tipo_cliente') == 'visitante' ? 'selected' : '' }}>Visitante</option>
                                    <option value="cliente" {{ old('tipo_cliente') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="proveedor" {{ old('tipo_cliente') == 'proveedor' ? 'selected' : '' }}>Proveedor</option>
                                </select>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2();

    const $tipoPersona = $("#tipo_persona");
    const $natural = $("#persona_natural");
    const $juridica = $("#persona_juridica");
    const $tipoId = $("#tipo_id");
    const $tipoCliente = $("#tipo_cliente");
    const $archivosInput = $("#archivos");
    const $filePreview = $("#filePreview");

    const opcionesNatural = {
        tipoId: `
            <option value="cc">C.C.</option>
            <option value="ce">C.E.</option>
            <option value="pa">P.A.</option>
            <option value="ppt">PPT</option>
            <option value="pep">PEP</option>
        `,
        tipoCliente: `
            <option value="contratista">Contratista</option>
            <option value="visitante">Visitante</option>
        `
    };

    const opcionesJuridica = {
        tipoId: `
            <option value="nit">NIT</option>
            <option value="internacional">INTERNACIONAL</option>
        `,
        tipoCliente: `
            <option value="cliente">Cliente</option>
            <option value="proveedor">Proveedor</option>
        `
    };

    function handlePersonTypeChange() {
        const isNatural = $tipoPersona.val() === "natural";
        
        // Mostrar/ocultar secciones
        $natural.toggle(isNatural);
        $juridica.toggle(!isNatural);
        
        // Actualizar opciones
        $tipoId.html(isNatural ? opcionesNatural.tipoId : opcionesJuridica.tipoId);
        $tipoCliente.html(isNatural ? opcionesNatural.tipoCliente : opcionesJuridica.tipoCliente);
        
        // Manejar required
        $('[name="nombre_completo"]').prop('required', isNatural);
        $archivosInput.prop('required', !isNatural);
        
        // Reiniciar Select2
        $tipoId.trigger('change');
        $tipoCliente.trigger('change');
    }

    // Eventos
    $tipoPersona.on("change", handlePersonTypeChange);
    handlePersonTypeChange(); // Inicializar

    // Validación de archivos
    $("#solicitudForm").on('submit', function(e) {
        if ($tipoPersona.val() === 'juridica') {
            const files = $archivosInput[0].files;
            
            if (files.length === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Debe adjuntar al menos un archivo para persona jurídica', 'error');
                return false;
            }
            
            // Resto de validaciones...
        }
    });

    // Vista previa de archivos
    $archivosInput.on('change', function() {
        $filePreview.empty();
        const files = this.files;
        
        if (files.length > 0) {
            const $fileList = $('<div>').addClass('list-group');
            
            $.each(files, function(i, file) {
                const $fileItem = $('<div>').addClass('file-item');
                const $fileInfo = $('<span>').text(`${file.name} (${(file.size / 1024).toFixed(2)} KB)`);
                const $removeBtn = $('<button>')
                    .addClass('btn btn-sm btn-danger')
                    .html('&times;')
                    .on('click', function(e) {
                        e.preventDefault();
                        const newFiles = Array.from(files).filter((_, idx) => idx !== i);
                        const dataTransfer = new DataTransfer();
                        newFiles.forEach(f => dataTransfer.items.add(f));
                        $archivosInput[0].files = dataTransfer.files;
                        $archivosInput.trigger('change');
                    });
                
                $fileItem.append($fileInfo, $removeBtn);
                $fileList.append($fileItem);
            });
            
            $filePreview.append($fileList);
        }
    });
});
</script>
@endsection