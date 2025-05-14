@extends('errors.errors_layout')

@section('title')
404 - Page Not Found
@endsection

@section('error-content')
    <h2>404</h2>
    <p>Pagína no encontrada</p>
    <a href="{{ route('admin.dashboard') }}">Regresar</a>
    <a href="{{ route('admin.login') }}">Ingresar!</a>
@endsection