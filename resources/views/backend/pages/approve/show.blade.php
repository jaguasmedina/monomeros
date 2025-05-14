@extends('backend.layouts.master')

@section('title', 'Revisar Solicitud')

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
            <h4 class="page-title">Revisar Solicitud</h4>
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Solicitud #{{ $solicitud->id }}</h4>
                    @include('backend.layouts.partials.messages')

                    {{-- Formulario de decisión --}}
                    <form action="{{ route('admin.approver.save', $solicitud->id) }}?vista={{ request('vista') }}" method="POST">
                        @csrf

                        {{-- 1) Número y Fecha --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Número de Solicitud</label>
                                <input type="text" readonly class="form-control" value="{{ $solicitud->id }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Fecha de Registro</label>
                                <input type="date" readonly class="form-control"
                                       value="{{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- 2) Razón Social e Identificación --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Razón Social</label>
                                <input type="text" readonly class="form-control" value="{{ $solicitud->razon_social }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Identificación</label>
                                <input type="text" readonly class="form-control"
                                       value="{{ $solicitud->tipo_id }} {{ $solicitud->identificador }}">
                            </div>
                        </div>

                        {{-- 3) Motivo y descarga del original --}}
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>Motivo</label>
                                <textarea readonly class="form-control" style="min-height:100px;">{{ $solicitud->motivo }}</textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Descargar Documento Original</label><br>
                                @if($solicitud->archivo)
                                    <a href="{{ asset('storage/' . $solicitud->archivo) }}"
                                       class="btn btn-success" target="_blank">
                                        <i class="fa fa-download"></i> PDF
                                    </a>
                                @else
                                    <p>No hay documento adjunto</p>
                                @endif
                            </div>
                        </div>

                        {{-- 4) Miembros y decisión --}}
                        <div id="membersContainer">
                            @foreach ($solicitud->miembros as $i => $miembro)
                                <div class="form-row member" data-miembro-id="{{ $miembro->id }}">
                                    <div class="form-group col-md-3">
                                        <label>Título</label>
                                        <input type="text" name="miembros[{{ $i }}][titulo]"
                                               class="form-control" value="{{ $miembro->titulo }}" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Nombre</label>
                                        <input type="text" name="miembros[{{ $i }}][nombre]"
                                               class="form-control" value="{{ $miembro->nombre }}" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Tipo ID</label>
                                        <select name="miembros[{{ $i }}][tipo_id]" class="form-control" required>
                                            <option value="cc"         {{ $miembro->tipo_id=='cc'?'selected':'' }}>C.C.</option>
                                            <option value="ce"         {{ $miembro->tipo_id=='ce'?'selected':'' }}>C.E.</option>
                                            <option value="pa"         {{ $miembro->tipo_id=='pa'?'selected':'' }}>P.A.</option>
                                            <option value="ppt"        {{ $miembro->tipo_id=='ppt'?'selected':'' }}>PPT</option>
                                            <option value="pep"        {{ $miembro->tipo_id=='pep'?'selected':'' }}>PEP</option>
                                            <option value="ti"         {{ $miembro->tipo_id=='ti'?'selected':'' }}>TI</option>
                                            <option value="rc"         {{ $miembro->tipo_id=='rc'?'selected':'' }}>RC</option>
                                            <option value="nit"        {{ $miembro->tipo_id=='nit'?'selected':'' }}>NIT</option>
                                            <option value="internacional" {{ $miembro->tipo_id=='internacional'?'selected':'' }}>INTERNACIONAL</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Número ID</label>
                                        <input type="text" name="miembros[{{ $i }}][numero_id]"
                                               class="form-control" value="{{ $miembro->numero_id }}" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>¿Favorable?</label>
                                        <select name="miembros[{{ $i }}][favorable]"
                                                class="form-control favorable-select" required>
                                            <option value="si" {{ $miembro->favorable=='si'?'selected':'' }}>Sí</option>
                                            <option value="no" {{ $miembro->favorable=='no'?'selected':'' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- 5) Concepto de No Favorable --}}
                        <div id="conceptoContainer"
                             class="form-group {{ $solicitud->miembros->where('favorable','no')->count()?'':'hidden' }}">
                            <label>Concepto de No Favorable</label>
                            <textarea name="concepto_no_favorable"
                                      class="form-control">{{ old('concepto_no_favorable', $solicitud->concepto_no_favorable ?? '') }}</textarea>
                        </div>

                        {{-- Botones de acción --}}
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="{{ request('vista') == 2
                                        ? route('admin.approver2.index')
                                        : route('admin.approver.index') }}"
                           class="btn btn-secondary ml-2">
                            Volver
                        </a>
                    </form>

                                        {{-- botón PDF final si quedó ENTREGADO, ya sea desde SAGRILAFT o PTEE --}}
                    @if($solicitud->estado === 'ENTREGADO')
                    <a href="{{ route('admin.service.documento.final', $solicitud->id) }}"
                    target="_blank"
                    class="btn btn-success ml-2">
                        <i class="fa fa-file-pdf-o"></i> PDF Final
                    </a>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mostrar/ocultar "Concepto de No Favorable"
    document.querySelectorAll('.favorable-select').forEach(select => {
        select.addEventListener('change', () => {
            const anyNo = Array.from(document.querySelectorAll('.favorable-select'))
                                 .some(s => s.value === 'no');
            document.getElementById('conceptoContainer')
                    .classList.toggle('hidden', !anyNo);
        });
    });
</script>
@endsection
