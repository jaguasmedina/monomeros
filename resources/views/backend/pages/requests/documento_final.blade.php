{{-- resources/views/backend/pages/requests/documento_final.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Debida Diligencia N. {{ $solicitud->id }} – {{ now()->format('Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 2cm;
            color: #333;
            background: #fff;
        }
        .header {
            position: relative;
            text-align: center;
            margin-bottom: 20px;
        }
        .header img.logo {
            height: 80px;
            margin-right: 20px;
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
            margin: 0 0 30px;
            color: #006837;
        }
        .content {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .concept-section {
            margin-bottom: 25px;
        }
        .concept-section h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .concept-section p {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px;
        }
        .concept-section small {
            display: block;
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
        // nombre de los oficiales
        $offS = Admin::where('username','sagrilaft')->value('name');
        $offP = Admin::where('username','ptee')->value('name');
        // flag si SAGRILAFT dictó No Favorable
        $noFavS = strtoupper($solicitud->concepto_sagrilaft ?? '') === 'NO FAVORABLE';
    @endphp

    <div class="header">
        @if($logo_existe)
            <img src="data:image/png;base64,{{ $logo }}" class="logo" alt="Logo Monómeros">
        @endif
        @if(!empty($qr_img_static))
            <img src="data:image/png;base64,{{ $qr_img_static }}" class="static-qr" alt="QR Fuentes">
        @endif
    </div>

    <div class="title">
        DEBIDA DILIGENCIA DE CONTRAPARTE N. {{ $solicitud->id }} – {{ now()->format('Y') }}
    </div>

    <div class="content">
        Monómeros S.A. y sus empresas filiales, luego de realizar la Debida Diligencia de 
        <strong>{{ $solicitud->nombre_completo ?? $solicitud->razon_social }}</strong>, 
        identificada con <strong>{{ $solicitud->tipo_id }} {{ $solicitud->identificador }}</strong>,
        emiten el siguiente concepto:
        <strong>{{ strtoupper($solicitud->concepto ?? '—') }}</strong>.
    </div>

    <div class="concept-section">
        <h3>Concepto de la revisión realizada para LA/FT/FPADM:</h3>
        <p>{{ strtoupper($solicitud->concepto_sagrilaft ?? '—') }}</p>
        <small>Oficial de Cumplimiento SAGRILAFT: {{ $offS }}</small>
    </div>

    @unless($noFavS)
        <div class="concept-section">
            <h3>Concepto de la revisión realizada para antecedentes de corrupción y soborno transnacional:</h3>
            <p>{{ strtoupper($solicitud->concepto_ptee ?? '—') }}</p>
            <small>Oficial de Cumplimiento C/ST: {{ $offP }}</small>
        </div>
    @endunless

    <div class="footer">
        <br>Fecha Debida Diligencia: {{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('d/m/Y') }}<br>
        NIT: 860.020.439-5<br>
        Tel: (5) 3618650 – 3618100 – A.A 3205<br>
        Vía 40 – Las Flores. Barranquilla, Atlántico.<br>
        Monómeros S.A. – Todos los derechos reservados
    </div>
</body>
</html>
