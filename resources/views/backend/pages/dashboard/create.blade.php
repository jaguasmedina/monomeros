
@extends('backend.layouts.master')

@section('title')
Crear usuario - Información PCP
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Crear Registro</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Aréa de Trabajo</a></li>
                    <li><a href="{{ route('admin.dashboard') }}"> 
                        Información PCP</a></li>
                    <li><span>Create Admin</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Crear un nuevo Registro</h4>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.informations.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="tipo">Tipo Documento</label>
                                <input type="text" class="form-control" id="identificador" name="identificador" placeholder="identificador" value="{{ old('identificador') }}" required autofocus style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="tipo">Tipo Documento</label>
                                <input type="text" class="form-control" id="tipo" name="tipo" placeholder="Tipo Documento" value="{{ old('tipo') }}" required autofocus style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="nombre_completo">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Nombre Completo" value="{{ old('nombre_completo') }}" required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="empresa">Nombre Empresa</label>
                                <input type="text" class="form-control" id="empresa" name="empresa" placeholder="Empresa" value="{{ old('empresa') }}" required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="fecha_registro">Fecha Registro</label>
                                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" placeholder="Fecha Registro" value="{{ old('fecha_registro') }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="fecha_vigencia">Fecha Vigencia</label>
                                <input type="date" class="form-control" id="fecha_vigencia" name="fecha_vigencia" placeholder="Fecha Vigencia" value="{{ old('fecha_vigencia') }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="cargo">Cargo</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo" value="{{ old('cargo') }}" required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="estado">Estado</label>
                                <input type="text" class="form-control" id="estado" name="estado" placeholder="Estado" value="{{ old('estado') }}" required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                            </div>
                        </div>



                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Guardar</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-4 pr-4 pl-4">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    })
</script>
@endsection
