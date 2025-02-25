@extends('backend.layouts.master')

@section('title', 'Cargar Excel - Panel administrador')

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Cargar Excel</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                    <li><span>Cargar Excel</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <div class="col-lg-6 offset-lg-3 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Subir Archivo Excel</h4>

                    @can('dashboard.edit') <!-- Verifica si el usuario tiene el permiso dashboard.edit -->
                        <form action="{{ route('admin.informations.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Seleccionar archivo:</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Cargar Excel</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
                        </form>
                    @else
                        <div class="alert alert-danger" role="alert">
                            No tienes permisos para realizar esta acción.
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection