@extends('backend.layouts.master')

@section('title', 'Crear Solicitud')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .hidden { display: none; }
        .file-preview { margin-top: 5px; }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            margin-bottom: 5px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .file-item button { padding: 2px 8px; }
    </style>
@endsection

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6"><h4 class="page-title">Crear Solicitud</h4></div>
    </div>
</div>
<div class="main-content-inner">
    <div class="row"><div class="col-12 mt-5"><div class="card"><div class="card-body">
        <h4 class="header-title">Nueva Solicitud</h4>
        @include('backend.layouts.partials.messages')
        @if (session('solicitud_id'))
            <div class="alert alert-info">
                ID de la solicitud creada: <strong>{{ session('solicitud_id') }}</strong>
            </div>
        @endif

        <form action="{{ route('admin.service.store') }}" method="POST"
              enctype="multipart/form-data" id="solicitudForm">
            @csrf

            {{-- 1) Tipo de Persona --}}
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="tipo_persona">Tipo de Persona *</label>
                    <select id="tipo_persona" name="tipo_persona"
                            class="form-control select2" required>
                        <option value="natural"
                            {{ old('tipo_persona')==='natural'? 'selected':'' }}>
                            PERSONA NATURAL
                        </option>
                        <option value="juridica"
                            {{ old('tipo_persona')==='juridica'? 'selected':'' }}>
                            PERSONA JURÍDICA
                        </option>
                    </select>
                </div>
            </div>

            {{-- 2) Fecha y Razón Social --}}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Fecha *</label>
                    <input type="date" name="fecha_registro" class="form-control"
                           value="{{ old('fecha_registro', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Razón Social *</label>
                    <input type="text" name="razon_social" class="form-control"
                           placeholder="Razón Social"
                           value="{{ old('razon_social') }}" required>
                </div>
            </div>

            {{-- 3) Tipo de ID y Número --}}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Tipo de ID *</label>
                    <select name="tipo_id" id="tipo_id"
                            class="form-control select2" required>
                        <option value="CC"          {{ old('tipo_id')==='CC'? 'selected':'' }}>C.C.</option>
                        <option value="CE"          {{ old('tipo_id')==='CE'? 'selected':'' }}>C.E.</option>
                        <option value="PA"          {{ old('tipo_id')==='PA'? 'selected':'' }}>P.A.</option>
                        <option value="PPT"         {{ old('tipo_id')==='PPT'? 'selected':'' }}>PPT</option>
                        <option value="PEP"         {{ old('tipo_id')==='PEP'? 'selected':'' }}>PEP</option>
                        <option value="TI"          {{ old('tipo_id')==='TI'? 'selected':'' }}>TI</option>
                        <option value="RC"          {{ old('tipo_id')==='RC'? 'selected':'' }}>RC</option>
                        <option value="NIT"         {{ old('tipo_id')==='NIT'? 'selected':'' }}>NIT</option>
                        <option value="INTERNACIONAL"{{ old('tipo_id')==='INTERNACIONAL'? 'selected':'' }}>INTERNACIONAL</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Número de ID *</label>
                    <input type="text" name="identificador" id="identificador"
                           class="form-control" placeholder="Identificador"
                           value="{{ old('identificador') }}" required maxlength="30">
                </div>
            </div>

            {{-- 4) Motivo --}}
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Motivo *</label>
                    <textarea name="motivo" class="form-control"
                              placeholder="Motivo" required>{{ old('motivo') }}</textarea>
                </div>
            </div>

            {{-- 5) Nombre Completo / Archivos --}}
            <div class="form-row">
                <div class="form-group col-md-6" id="persona_natural">
                    <label>Nombre Completo *</label>
                    <input type="text" name="nombre_completo" id="nombre_completo"
                           class="form-control"
                           placeholder="Nombre Completo"
                           value="{{ old('nombre_completo') }}">
                </div>
                <div class="form-group col-md-6 hidden" id="persona_juridica">
                    <label>Subir Archivo(s) (PDF) * <small>(hasta 10)</small></label>
                    <input type="file" name="archivos[]" id="archivos"
                           class="form-control" accept="application/pdf" multiple>
                    <div id="filePreview" class="file-preview"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Tipo de Cliente *</label>
                    <select name="tipo_cliente" id="tipo_cliente"
                            class="form-control select2" required>
                        <option value="CONTRATISTA" {{ old('tipo_cliente')==='CONTRATISTA'? 'selected':'' }}>CONTRATISTA</option>
                        <option value="VISITANTE"   {{ old('tipo_cliente')==='VISITANTE'?   'selected':'' }}>VISITANTE</option>
                        <option value="CLIENTE"     {{ old('tipo_cliente')==='CLIENTE'?     'selected':'' }}>CLIENTE</option>
                        <option value="PROVEEDOR"   {{ old('tipo_cliente')==='PROVEEDOR'?   'selected':'' }}>PROVEEDOR</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div></div></div></div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(function(){
        // Inicializar Select2
        $('.select2').select2();

        // Elementos
        const $tipoPersona = $('#tipo_persona'),
              $natural     = $('#persona_natural'),
              $juridica    = $('#persona_juridica'),
              $tipoId      = $('#tipo_id'),
              $tipoCliente = $('#tipo_cliente'),
              $archivos    = $('#archivos'),
              $preview     = $('#filePreview');

        // Opciones para cada tipo de persona
        const optsNatural = {
            tipoId: `
                <option value="CC">C.C.</option>
                <option value="CE">C.E.</option>
                <option value="PA">P.A.</option>
                <option value="PPT">PPT</option>
                <option value="PEP">PEP</option>
                <option value="TI">TI</option>
                <option value="RC">RC</option>
            `,
            tipoCli: `
                <option value="CONTRATISTA">CONTRATISTA</option>
                <option value="VISITANTE">VISITANTE</option>
            `
        };
        const optsJuridica = {
            tipoId: `
                <option value="NIT">NIT</option>
                <option value="INTERNACIONAL">INTERNACIONAL</option>
            `,
            tipoCli: `
                <option value="CLIENTE">CLIENTE</option>
                <option value="PROVEEDOR">PROVEEDOR</option>
            `
        };

        // Mostrar/ocultar secciones según tipo de persona
        function onTipoChange(){
            const isNatural = $tipoPersona.val() === 'natural';
            $natural.toggle(isNatural);
            $juridica.toggle(!isNatural);
            $tipoId.html(isNatural ? optsNatural.tipoId : optsJuridica.tipoId).trigger('change');
            $tipoCliente.html(isNatural ? optsNatural.tipoCli : optsJuridica.tipoCli).trigger('change');
            $('#nombre_completo').prop('required', isNatural);
            $archivos.prop('required', !isNatural);
        }
        $tipoPersona.on('change', onTipoChange);
        onTipoChange();

        // Vista previa de archivos PDF
        $archivos.on('change', function(){
            $preview.empty();
            Array.from(this.files).forEach((file, i) => {
                const $item = $(`
                    <div class="file-item">
                        <span>${file.name} (${(file.size/1024).toFixed(2)} KB)</span>
                        <button type="button">&times;</button>
                    </div>
                `);
                $item.find('button').on('click', () => {
                    const dt = new DataTransfer();
                    Array.from($archivos[0].files)
                        .filter((_, idx) => idx !== i)
                        .forEach(f => dt.items.add(f));
                    $archivos[0].files = dt.files;
                    $archivos.trigger('change');
                });
                $preview.append($item);
            });
        });

        // Forzar mayúsculas en todos los text inputs y textareas
        $('input[type="text"], textarea').on('input', function(){
            this.value = this.value.toUpperCase();
        });

        // Validación de archivos al enviar
        $('#solicitudForm').on('submit', function(e){
            if ($tipoPersona.val()==='juridica' && $archivos[0].files.length===0){
                e.preventDefault();
                Swal.fire('Error','Debe adjuntar al menos un PDF','error');
            }
        });
    });
    </script>
@endsection
