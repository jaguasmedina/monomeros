
@extends('backend.layouts.master')

@section('title')
Editar usuario - panel administrador
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
                <h4 class="page-title pull-left">Editar Usuario</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Aréa de Trabajo</a></li>
                    <li><a href="{{ route('admin.dashboard') }}">Todos los usuarios</a></li>
                    <li><span>Usuario - {{ $usuario->nombre_completo }}</span></li>
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
                    <h4 class="header-title">Editar Usuario - {{ $usuario->nombre_completo }}</h4>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.informations.update', $usuario->identificador) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">Tipo Documento</label>
                                <input type="text" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" id="tipo" name="tipo" placeholder="Tipo Documento" value="{{ $usuario->tipo }}" required autofocus>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="email">Nombre Completo</label>
                                <input type="text" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Nombre Completo" value="{{ $usuario->nombre_completo }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="email">Nombre Empresa</label>
                                <input type="text" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" id="empresa" name="empresa" placeholder="Empresa" value="{{ $usuario->empresa }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="email">Fecha Registro</label>
                                <input type="date" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" id="fecha_registro" name="fecha_registro" placeholder="Fecha Registro" value="{{ $usuario->fecha_registro }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="email">Fecha Vigencia</label>
                                <input type="date" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" id="fecha_vigencia" name="fecha_vigencia" placeholder="Fecha Vigencia" value="{{ $usuario->fecha_vigencia }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="email">Cargo</label>
                                <input type="text" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" class="form-control" id="cargo" name="cargo" placeholder="Cargo" value="{{ $usuario->cargo }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="estado">Concepto</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="FAVORABLE" {{ $usuario->estado == 'FAVORABLE' ? 'selected' : '' }}>FAVORABLE</option>
                                    <option value="NO FAVORABLE" {{ $usuario->estado == 'NO FAVORABLE' ? 'selected' : '' }}>NO FAVORABLE</option>
                                </select>
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
