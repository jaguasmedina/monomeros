<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Previsualización - Debida Diligencia</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 2cm;
            background-color: #f7f7f7;
            color: #333;
            line-height: 1.6;
        }
        .container {
            background-color: #fff;
            padding: 2cm;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-width: 800px;
            margin: 20px auto;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 1.5cm;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1cm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            font-size: 12pt;
            margin-bottom: 2cm;
        }
        .section {
            margin-bottom: 1.5cm;
        }
        .concept-box {
            border: 1px solid #007BFF;
            padding: 15px;
            margin-top: 1cm;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .concept-title {
            font-weight: bold;
            margin-bottom: 0.5cm;
            font-size: 12pt;
            color: #007BFF;
        }
        .footer {
            text-align: right;
            font-size: 10pt;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #28A745;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="title">
                DEBIDA DILIGENCIA DE CONTRAPARTE N. {{ $solicitud->id }} – {{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('Y') }}
            </div>
        </div>

        <!-- Contenido del documento -->
        <div class="content">
            <div class="section">
                <p>
                    Monómeros y sus empresas filiales y luego de realizar la Debida Diligencia de 
                    <strong>{{ $solicitud->nombre_completo ?? $solicitud->razon_social }}</strong> 
                    Identificada con NÚMERO <strong>{{ $solicitud->identificador }}</strong>
                </p>
            </div>

            <div class="section">
                <p>
                    Se emite el siguiente concepto: <strong>{{ $solicitud->concepto ?? 'N/A' }}</strong>
                </p>
            </div>

            <div class="section">
                <p><strong>Concepto de la revisión realizada para LA/FT/FPADM:</strong></p>
                <div class="concept-box">
                    <p>
                        Concepto: <strong>{{ $solicitud->concepto_sagrilaft ?? '___________________' }}</strong>
                    </p>
                    <p>OFICIAL DE CUMPLIMIENTO SAGRILAFT</p>
                </div>
            </div>

            <div class="section">
                <p><strong>Concepto de la revisión realizada para antecedentes de corrupción y soborno transnacional:</strong></p>
                <div class="concept-box">
                    <p>
                        Concepto: <strong>{{ $solicitud->concepto_ptee ?? '___________________' }}</strong>
                    </p>
                    <p>OFICIAL DE CUMPLIMIENTO C/ST</p>
                </div>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            Fecha Debida Diligencia: {{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('d/m/Y') }}
        </div>

        <!-- Botón para descargar el PDF -->
        <div style="text-align: center;">
            <a href="{{ route('admin.service.documento.final', $solicitud->id) }}" class="btn">Descargar PDF</a>
        </div>
    </div>
</body>
</html>
