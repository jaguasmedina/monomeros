<?php

namespace App\Exports;

use App\Models\Miembro;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MiembrosExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Miembro::query();

        if (!empty($this->filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $this->filters['nombre'] . '%');
        }
        if (!empty($this->filters['tipo_id'])) {
            $query->where('tipo_id', $this->filters['tipo_id']);
        }
        if (!empty($this->filters['favorable'])) {
            $query->where('favorable', $this->filters['favorable']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Solicitud ID',
            'Título',
            'Nombre',
            'Tipo ID',
            'Número ID',
            'Favorable',
            'Concepto No Favorable',
            'Created At',
            'Updated At'
        ];
    }
}
