<?php

namespace App\Exports;

use App\Models\Information;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InformationExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Information::query();

        if (!empty($this->filters['fecha_inicio'])) {
            $query->where('fecha_registro', '>=', $this->filters['fecha_inicio']);
        }
        if (!empty($this->filters['fecha_fin'])) {
            $query->where('fecha_registro', '<=', $this->filters['fecha_fin']);
        }
        if (!empty($this->filters['empresa'])) {
            $query->where('empresa', 'like', '%' . $this->filters['empresa'] . '%');
        }
        if (!empty($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'Identificador',
            'Tipo',
            'Nombre Completo',
            'Empresa',
            'Fecha Registro',
            'Fecha Vigencia',
            'Cargo',
            'Estado',
            'Created At',
            'Updated At'
        ];
    }
}
