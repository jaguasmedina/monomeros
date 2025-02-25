@extends('backend.auth.auth_master')

@section('auth_title')
    Login | Panel Administrador
@endsection

@section('auth-content')
     <!-- login area start -->
     <div class="login-area">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    <div class="login-form-head">
                        <!-- Logo Monómeros -->
                        <div class="logo-container" style="text-align: center; margin-bottom: 20px;">
                            <img src="https://www.monomeros.com.co/wp-content/uploads/LOGO-BLANCO-MONOMEROS-1-1024x576.png" 
                                alt="Logo Monómeros" 
                                style="max-width: 200px; height: auto;">
                        </div>
                        <h4>Iniciar Sesión</h4>
                        <p style="font: bold">PANEL ADMINISTRATIVO - SUPERINTENDENCIA PCP</p>
                    </div>
                    <div class="login-form-body">
                        @include('backend.layouts.partials.messages')
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Usuario</label>
                            <input type="text" id="exampleInputEmail1" name="email">
                            <i class="ti-email"></i>
                            <div class="text-danger"></div>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Contraseña</label>
                            <input type="password" id="exampleInputPassword1" name="password">
                            <i class="ti-lock"></i>
                            <div class="text-danger"></div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" id="customControlAutosizing" name="remember">
                                    <label class="custom-control-label" for="customControlAutosizing">Recordarme</label>
                                </div>
                            </div>
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Acceder<i class="ti-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->
@endsection
