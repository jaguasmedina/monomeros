{{-- resources/views/backend/pages/requests/documento_final.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Debida Diligencia N.º {{ $solicitud->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 2cm;
            color: #333;
            background: #f7f7f7;
        }
        .header {
            position: relative;
            text-align: center;
            margin-bottom: 20px;
        }
        .header img.logo {
            height: 60px;
            margin: 0 15px;
        }
        .header img.dynamic-qr {
            height: 60px;
            margin: 0 15px;
        }
        .header img.static-qr {
            position: absolute;
            top: 0;
            right: 0;
            height: 80px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 30px;
            color: #006837;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .info-table th,
        .info-table td {
            text-align: left;
            padding: 8px 12px;
        }
        .info-table th {
            background: #e6f2e6;
            width: 30%;
        }
        .concept-box {
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .concept-box.favorable {
            background: #e6ffed;
            border: 1px solid #8fd19e;
        }
        .concept-box.unfavorable {
            background: #ffe6e6;
            border: 1px solid #d19e9e;
        }
        .concept-box h3 {
            margin-top: 0;
            font-size: 14px;
            color: #333;
        }
        .concept-box p {
            font-size: 16px;
            font-weight: bold;
            margin: 8px 0;
        }
        .concept-box small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #555;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    @php
        use App\Models\Admin;

        // Nombre de los oficiales
        $offS = Admin::where('username','sagrilaft')->value('name');
        $offP = Admin::where('username','ptee')->value('name');

        // ¿SAGRILAFT dictó No Favorable?
        $noFavS = strtoupper($solicitud->concepto_sagrilaft ?? '') === 'NO FAVORABLE';
    @endphp

    <div class="header">
        {{-- Logo dinámico --}}
        @if($logo_existe)
            <img src="data:image/png;base64,{{ $logo }}" class="logo" alt="Logo">
        @endif

        {{-- QR dinámico antiguo (opcional) --}}
        @if($rq_existe)
            <img src="data:image/png;base64,{{ $rq }}" class="dynamic-qr" alt="QR Dinámico">
        @endif

        {{-- QR estático desde public/qr.png --}}
        @if(!empty($qr_img_static))
            <img src="data:image/png;base64,{{ $qr_img_static }}" class="static-qr" alt="QR Estático">
        @endif
    </div>

    <div class="title">
        DEBIDA DILIGENCIA – CONTRAPARTE N.º {{ $solicitud->id }}
    </div>

    <table class="info-table">
        <tr>
            <th>Fecha de Registro</th>
            <td>{{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Razón Social / Nombre</th>
            <td>{{ $solicitud->nombre_completo ?? $solicitud->razon_social }}</td>
        </tr>
        <tr>
            <th>Identificación</th>
            <td>{{ $solicitud->tipo_id }} {{ $solicitud->identificador }}</td>
        </tr>
        <tr>
            <th>Motivo de Solicitud</th>
            <td>{{ $solicitud->motivo }}</td>
        </tr>
        <tr>
            <th>Concepto Global</th>
            <td>{{ strtoupper($solicitud->concepto ?? '—') }}</td>
        </tr>
    </table>

    {{-- Concepto SAGRILAFT --}}
    <div class="concept-box {{ $noFavS ? 'unfavorable' : 'favorable' }}">
        <h3>Concepto SAGRILAFT</h3>
        <p>{{ strtoupper($solicitud->concepto_sagrilaft ?? '—') }}</p>
        <small>Oficial de Cumplimiento SAGRILAFT: {{ $offS }}</small>
    </div>

    {{-- Concepto PTEE solo si SAGRILAFT no dictó No Favorable --}}
    @unless($noFavS)
        <div class="concept-box {{ strtoupper($solicitud->concepto_ptee ?? '') === 'NO FAVORABLE' ? 'unfavorable' : 'favorable' }}">
            <h3>Concepto PTEE</h3>
            <p>{{ strtoupper($solicitud->concepto_ptee ?? '—') }}</p>
            <small>Oficial de Cumplimiento PTEE: {{ $offP }}</small>
        </div>
    @endunless

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }}<br>
        Monómeros S.A. – Todos los derechos reservados
    </div>
</body>
</html>
