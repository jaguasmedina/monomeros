<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Documento de Debida Diligencia</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
        }
        .header img { 
            height: 80px;
            max-width: 150px;
            object-fit: contain;
        }
        .title { 
            font-size: 16px; 
            font-weight: bold; 
            text-align: center; 
            margin: 25px 0;
            text-transform: uppercase;
        }
        .content { 
            font-size: 14px; 
            line-height: 1.6;
            margin-bottom: 30px;
            /* Justificado */
            text-align: justify;
            text-justify: inter-word;
            -webkit-text-align: justify;
        }
        .concepts-container {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            gap: 30px;
        }
        .concept-box {
            width: 48%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .concept-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
            color: #333;
        }
        .concept-value {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #2c5e9c;
        }
        .official-name {
            margin-top: 20px;
            font-weight: bold;
            text-align: right;
            font-style: italic;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
            color: #666;
        }
        .document-info {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://raw.githubusercontent.com/jaguasmedina/monomeros/ac4af51e01dffd9f458e0ecc64de30afa9dc5d19/storage/app/public/logo.png" alt="Logo">
        <img src="https://raw.githubusercontent.com/jaguasmedina/monomeros/ac4af51e01dffd9f458e0ecc64de30afa9dc5d19/storage/app/public/qr.png" alt="QR">
    </div>

    <div class="title">DEBIDA DILIGENCIA DE CONTRAPARTE N. {{ $solicitud->id }} -- {{ date('Y') }}</div>

    <div class="content">
        Monómeros y sus empresas filiales y luego de realizar la Debida Diligencia de {{ $solicitud->nombre_completo ?? $solicitud->razon_social }}, identificada con NÚMERO {{ $solicitud->tipo_id }} {{ $solicitud->identificador }}, se emite el siguiente concepto: <strong>{{ $solicitud->concepto }}</strong>
    </div>

    <div class="concepts-container">
        <div class="concept-box">
            <div class="concept-title">Concepto de la revisión realizada para LA/FT/FPADM:</div>
            <div class="concept-value">
                {{ $solicitud->concepto_sagrilaft ?? '___________________' }}
            </div>
            <div class="official-name">OFICIAL DE CUMPLIMIENTO SAGRILAFT</div>
        </div>
        
        <div class="concept-box">
            <div class="concept-title">Concepto de la revisión realizada para antecedentes de corrupción y soborno transnacional:</div>
            <div class="concept-value">
                {{ $solicitud->concepto_ptee ?? '___________________' }}
            </div>
            <div class="official-name">OFICIAL DE CUMPLIMIENTO C/ST</div>
        </div>
    </div>

    <div class="footer">
        <div class="document-info">Fecha Debida Diligencia: {{ $solicitud->fecha_registro }}</div>
        <div>Documento generado electrónicamente - Monómeros</div>
    </div>
</body>
</html>
