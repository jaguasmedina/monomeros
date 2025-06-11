<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de tu solicitud #{{ $solicitud->id }}</title>
</head>
<body>
    <p>Hola {{ $solicitud->admin->name }},</p>

    <p>El estado de tu solicitud <strong>#{{ $solicitud->id }}</strong> ha sido actualizado a:</p>

    <p style="font-size:1.2em; font-weight:bold; color:#006837;">
        {{ ucfirst(strtolower($solicitud->estado)) }}
    </p>

    <p>
        Razón Social: {{ $solicitud->razon_social }}<br>
        Fecha Registro: {{ \Carbon\Carbon::parse($solicitud->fecha_registro)->format('d/m/Y') }}<br>
        Motivo: {{ $solicitud->motivo }}<br>
        Razón Devolución: {{ $solicitud->motivo_rechazo }}
    </p>

    <p>Si tienes alguna duda, ingresa al sistema para más detalles.</p>

    <p>Saludos,<br>Equipo de Monómeros</p>
</body>
</html>
