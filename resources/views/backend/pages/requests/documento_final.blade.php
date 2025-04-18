<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Documento de Debida Diligencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 2cm;
            background-color: #fff;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 80px;
            margin: 0 10px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .content {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .concept-section {
            margin: 20px 0;
            padding: 10px;
            /* Quitamos fondo y bordes llamativos para un look más limpio */
        }
        .concept-section p {
            margin: 5px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://raw.githubusercontent.com/jaguasmedina/monomeros/ac4af51e01dffd9f458e0ecc64de30afa9dc5d19/storage/app/public/logo.png" alt="Logo">
        <img src="https://raw.githubusercontent.com/jaguasmedina/monomeros/ac4af51e01dffd9f458e0ecc64de30afa9dc5d19/storage/app/public/qr.png" alt="QR">
    </div>

    <div class="title">
        DEBIDA DILIGENCIA DE CONTRAPARTE N. {{ $solicitud->id }} -- {{ date('Y') }}
    </div>

    <div class="content">
        <p>
            Monómeros y sus empresas filiales y luego de realizar la Debida
            Diligencia de <strong>{{ $solicitud->nombre_completo ?? $solicitud->razon_social }}</strong>,
            Identificada con NÚMERO {{ $solicitud->tipo_id }} {{ $solicitud->identificador }}
        </p>
        <p>
            Se emite el siguiente concepto: <strong>{{ $solicitud->concepto }}</strong>
        </p>
    </div>

    <div class="concept-section">
        <p><strong>Concepto de la revisión realizada para LA/FT/FPADM:</strong></p>
        <p>
            Concepto: <strong>{{ $solicitud->concepto_sagrilaft ?? '___________________' }}</strong>
        </p>
        <p>
            OFICIAL DE CUMPLIMIENTO SAGRILAFT
        </p>
    </div>

    <div class="concept-section">
        <p><strong>Concepto de la revisión realizada para antecedentes de corrupción y soborno transnacional:</strong></p>
        <p>
            Concepto: <strong>{{ $solicitud->concepto_ptee ?? '___________________' }}</strong>
        </p>
        <p>
            OFICIAL DE CUMPLIMIENTO C/ST
        </p>
    </div>

    <div class="footer">
        Fecha Debida Diligencia: {{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('d/m/Y') }}
    </div>
</body>
</html>
