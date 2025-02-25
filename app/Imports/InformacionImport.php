<?php

namespace App\Imports;

use App\Models\information;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InformacionImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   if ($this->isRowEmpty($row)) {
            \Log::info('Fila vacía detectada, ignorada.');
            return null;
        }

   //     \Log::info('Procesando fila: ', $row);
        if (!isset($row['identificador']) || empty(trim($row['identificador']))) {
            return null;
        }
        if (empty(array_filter($row))) {
            return null;
        }
        if (information::where('identificador', $row['identificador'])->exists()) {
            return null;
        }
        $fecha_registro = $this->transformDate($row['fecha_registro']);
        $fecha_vigencia = $this->transformDate($row['fecha_vigencia']);
       // \Log::info('Fila procesada: ', $row);
        return new information([
            'identificador'   => $row['identificador'],
            'tipo'            => $row['tipo'],
            'nombre_completo' => $row['nombre'],
            'empresa'         => $row['empresa'],
            'fecha_registro'  => $fecha_registro,
            'fecha_vigencia'  => $fecha_vigencia,
            'cargo'           => $row['cargo'],
            'estado'          => $row['estado'],
        ]);
    }
    private function transformDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            if (is_numeric($date)) {
                return Carbon::createFromFormat('Y-m-d', Carbon::parse("1899-12-30")->addDays($date)->format('Y-m-d'));
            }
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::error("Error al convertir fecha: " . $date);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'identificador'   => 'required|distinct|unique:informacion,identificador',
            'tipo'            => 'required',
            'nombre'          => 'required',
            'empresa'         => 'required',
            'fecha_registro'  => 'required',
            'fecha_vigencia'  => 'required',
            'cargo'           => 'required',
            'estado'          => 'required',
        ];
    }
    public function customValidationMessages()
    {
        return [
            '0.required' => 'El identificador es obligatorio.',
            '1.required' => 'El tipo es obligatorio.',
            '2.required' => 'El nombre es obligatorio.',
            '3.required' => 'La empresa es obligatoria.',
            '4.required' => 'La fecha de registro es obligatoria y debe estar en formato dd/mm/yyyy.',
            '5.required' => 'La fecha de vigencia es obligatoria y debe estar en formato dd/mm/yyyy.',
            '6.required' => 'El cargo es obligatorio.',
            '7.required' => 'El estado es obligatorio.',
        ];
    }

    private function isRowEmpty($row)
    {
        foreach ($row as $key => $value) {
            if (!is_null($value) && trim($value) !== '') {
                return false; // La fila tiene al menos un valor válido
            }
        }
        return true;
    }
}
