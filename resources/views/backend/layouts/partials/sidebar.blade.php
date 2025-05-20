@php
    use Illuminate\Support\Facades\Auth;
    $usr = Auth::guard('admin')->user();
@endphp

<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <h2 class="text-white">Admin</h2>
            </a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    {{-- Superadmin --}}
                    @if($usr->hasRole('superadmin'))
                        <li class="active">
                            <a href="javascript:void(0)" aria-expanded="true">
                                <i class="ti-dashboard"></i>
                                <span>Registros</span>
                            </a>
                            <ul class="collapse in">
                                <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                                    <a href="{{ route('admin.dashboard') }}">INFORMACIÓN PCP</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript:void(0)" aria-expanded="true">
                                <i class="fa fa-tasks"></i>
                                <span>Roles y Permisos</span>
                            </a>
                            <ul class="collapse {{ Route::is('admin.roles.*') ? 'in' : '' }}">
                                @if ($usr->can('role.view'))
                                    <li class="{{ Route::is('admin.roles.index') ? 'active' : '' }}">
                                        <a href="{{ route('admin.roles.index') }}">Todos los roles</a>
                                    </li>
                                @endif
                                @if ($usr->can('role.create'))
                                    <li class="{{ Route::is('admin.roles.create') ? 'active' : '' }}">
                                        <a href="{{ route('admin.roles.create') }}">Crear Rol</a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <li>
                            <a href="javascript:void(0)" aria-expanded="true">
                                <i class="fa fa-user"></i>
                                <span>Usuarios</span>
                            </a>
                            <ul class="collapse {{ Route::is('admin.admins.*') ? 'in' : '' }}">
                                @if ($usr->can('admin.view'))
                                    <li class="{{ Route::is('admin.admins.index') ? 'active' : '' }}">
                                        <a href="{{ route('admin.admins.index') }}">Listado Usuarios</a>
                                    </li>
                                @endif
                                @if ($usr->can('admin.create'))
                                    <li class="{{ Route::is('admin.admins.create') ? 'active' : '' }}">
                                        <a href="{{ route('admin.admins.create') }}">Crear Usuario</a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <li class="{{ Route::is('admin.informations.upload_excel') ? 'active' : '' }}">
                            <a href="{{ route('admin.informations.upload_excel') }}">
                                <i class="ti-upload"></i>
                                <span>Cargar Excel</span>
                            </a>
                        </li>

                        @if($usr->can('log.view'))
                            <li class="{{ Route::is('admin.logs.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.logs.index') }}">
                                    <i class="fa fa-tasks"></i>
                                    Listado de Logs
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="javascript:void(0)" aria-expanded="true">
                                <i class="ti-clipboard"></i>
                                <span>Solicitudes</span>
                            </a>
                            <ul class="collapse">
                                <li class="{{ Route::is('admin.service.request') ? 'active' : '' }}">
                                    <a href="{{ route('admin.service.request') }}">Solicitar</a>
                                </li>
                                <li class="{{ Route::is('admin.service.query') ? 'active' : '' }}">
                                    <a href="{{ route('admin.service.query') }}">Consultar</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Route::is('admin.analists.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.analists.index') }}">
                                <i class="fa fa-list"></i>
                                Listado Solicitudes
                            </a>
                        </li>

                        <li class="{{ Route::is('admin.approver.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.approver.index') }}">
                                <i class="fa fa-list"></i>
                                Aprobador SAGRILAFT
                            </a>
                        </li>

                        <li class="{{ Route::is('admin.approver2.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.approver2.index') }}">
                                <i class="fa fa-list"></i>
                                Aprobador PTEE
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)" aria-expanded="false">
                                <i class="ti-bar-chart"></i>
                                <span>Reportes y Estadísticas</span>
                            </a>
                            <ul class="collapse">
                                <li class="{{ Route::is('admin.reports.informacion') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reports.informacion') }}">Información</a>
                                </li>
                                <li class="{{ Route::is('admin.reports.solicitudes') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reports.solicitudes') }}">Solicitudes</a>
                                </li>
                                <li class="{{ Route::is('admin.reports.miembros') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reports.miembros') }}">Miembros</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Route::is('admin.movimientos.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.movimientos.index') }}">
                                <i class="ti-list"></i>
                                <span>Historial Movimientos</span>
                            </a>
                        </li>

                    {{-- Visualizador --}}
                    @elseif($usr->hasRole('visualizador'))
                        <li class="{{ Route::is('admin.visualizador.report') ? 'active' : '' }}">
                            <a href="{{ route('admin.visualizador.report') }}">
                                <i class="ti-eye"></i>
                                <span>Visualizador</span>
                            </a>
                        </li>

                    {{-- Analista --}}
                    @elseif($usr->hasRole('analista'))
                        <li class="{{ Route::is('admin.analists.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.analists.index') }}">
                                <i class="fa fa-list"></i>
                                Listado Solicitudes
                            </a>
                        </li>
                        <li class="{{ Route::is('admin.movimientos.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.movimientos.index') }}">
                                <i class="ti-list"></i>
                                <span>Historial Movimientos</span>
                            </a>
                        </li>

                    {{-- SAGRILAFT --}}
                    @elseif($usr->hasRole('sagrilaft'))
                        <li class="{{ Route::is('admin.approver.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.approver.index') }}">
                                <i class="fa fa-list"></i>
                                Aprobador SAGRILAFT
                            </a>
                        </li>

                    {{-- PTEE --}}
                    @elseif($usr->hasRole('ptee'))
                        <li class="{{ Route::is('admin.approver2.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.approver2.index') }}">
                                <i class="fa fa-list"></i>
                                Aprobador PTEE
                            </a>
                        </li>

                    {{-- Usuarios --}}
                    @elseif($usr->hasRole('usuarios'))
                        <li class="{{ Route::is('admin.service.request') ? 'active' : '' }}">
                            <a href="{{ route('admin.service.request') }}">
                                <i class="ti-pencil-alt"></i>
                                <span>Crear Solicitud</span>
                            </a>
                        </li>
                        <li class="{{ Route::is('admin.mis_solicitudes') ? 'active' : '' }}">
                            <a href="{{ route('admin.mis_solicitudes') }}">
                                <i class="ti-eye"></i>
                                <span>Mis Solicitudes</span>
                            </a>
                        </li>
                    @endif

                    {{-- Siempre visible: Cerrar sesión --}}
                    <li>
                        <a href="{{ route('admin.logout.submit') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out"></i>
                            <span>Cerrar sesión</span>
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout.submit') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>
