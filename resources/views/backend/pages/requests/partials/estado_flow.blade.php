{{-- resources/views/backend/pages/requests/partials/estado_flow.blade.php --}}
@php
    $estadoUpper = strtoupper($estado);
@endphp

@if($estadoUpper == 'ENVIADO')
    <tr>
        <td colspan="{{ $colspan }}">
            <div class="flow-container">
                <div class="flow-line"></div>
                <div class="flow-step">
                    <div class="step-icon active">1</div>
                    <div class="step-name active-text">Enviado</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">2</div>
                    <div class="step-name">En Revisión</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">3</div>
                    <div class="step-name">Devuelto por Documentación</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">4</div>
                    <div class="step-name">Entregado</div>
                    <div class="step-detail"></div>
                </div>
            </div>
        </td>
    </tr>
@elseif(in_array($estadoUpper, ['APROBADOR_SAGRILAFT', 'APROBADOR_PTEE']))
    <tr>
        <td colspan="{{ $colspan }}">
            <div class="flow-container">
                <div class="flow-line"></div>
                <div class="flow-step">
                    <div class="step-icon inactive">1</div>
                    <div class="step-name">Enviado</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon active">2</div>
                    <div class="step-name active-text">En Revisión</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">3</div>
                    <div class="step-name">Devuelto por Documentación</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">4</div>
                    <div class="step-name">Entregado</div>
                    <div class="step-detail"></div>
                </div>
            </div>
        </td>
    </tr>
@elseif($estadoUpper == 'DOCUMENTACION')
    <tr>
        <td colspan="{{ $colspan }}">
            <div class="flow-container">
                <div class="flow-line"></div>
                <div class="flow-step">
                    <div class="step-icon inactive">1</div>
                    <div class="step-name">Enviado</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">2</div>
                    <div class="step-name">En Revisión</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon active">3</div>
                    <div class="step-name active-text">Devuelto por Documentación</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">4</div>
                    <div class="step-name">Entregado</div>
                    <div class="step-detail"></div>
                </div>
            </div>
        </td>
    </tr>
@elseif($estadoUpper == 'ENTREGADO')
    <tr>
        <td colspan="{{ $colspan }}">
            <div class="flow-container">
                <div class="flow-line"></div>
                <div class="flow-step">
                    <div class="step-icon inactive">1</div>
                    <div class="step-name">Enviado</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">2</div>
                    <div class="step-name">En Revisión</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon inactive">3</div>
                    <div class="step-name">Devuelto por Documentación</div>
                    <div class="step-detail"></div>
                </div>
                <div class="flow-step">
                    <div class="step-icon active">4</div>
                    <div class="step-name active-text">Entregado</div>
                    <div class="step-detail"></div>
                </div>
            </div>
        </td>
    </tr>
@endif
