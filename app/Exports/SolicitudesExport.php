<?php

namespace App\Exports;

use App\Models\Solicitud;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SolicitudesExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Solicitud::query();

        if (!empty($this->filters['fecha_inicio'])) {
            $query->where('fecha_registro', '>=', $this->filters['fecha_inicio']);
        }
        if (!empty($this->filters['fecha_fin'])) {
            $query->where('fecha_registro', '<=', $this->filters['fecha_fin']);
        }
        if (!empty($this->filters['razon_social'])) {
            $query->where('razon_social', 'like', '%' . $this->filters['razon_social'] . '%');
        }
        if (!empty($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Solicitud',
            'Tipo de Persona',
            'Fecha Registro',
            'Razón Social',
            'Tipo ID',
            'Identificador',
            'Motivo',
            'Nombre Completo',
            'Tipo Cliente',
            'Estado',
            'Created At',
            'Updated At'
        ];
    }
}
