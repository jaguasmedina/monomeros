@extends('errors.errors_layout')

@section('title')
    403 - Access Denied
@endsection

@section('error-content')
    <h2>403</h2>
    <p>Acceso denegado</p>
    <hr>
    <p class="mt-2">
        {{ $exception->getMessage() }}
    </p>
    <a href="{{ route('admin.dashboard') }}">Regresar</a>
    <a href="{{ route('admin.login') }}">Ingresar</a>
@endsection