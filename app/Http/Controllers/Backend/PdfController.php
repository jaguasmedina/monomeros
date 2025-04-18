<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    public function generarPDF($id)
    {
        $usuario = Solicitud::findOrFail($id);
        $templatePath = storage_path('app/public/formato.docx');

        if (!file_exists($templatePath)) {
            Log::error('No se encontró la plantilla en: ' . $templatePath);
            return response()->json(['error' => 'Plantilla no encontrada'], 404);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Validar datos antes de reemplazar
        Log::info('Valores obtenidos de la BD:', [
            'nombre' => $usuario->razon_social,
            'fecha' => date('d/m/Y'),
            'año' => date('Y'),
            'identificador' => $usuario->identificador,
            'id' => $id,
            'estado' => $usuario->estado,
        ]);

        // Reemplazo de valores
        $templateProcessor->setValue('{NOMBRE}', $usuario->razon_social);
        $templateProcessor->setValue('{FECHA}', date('d/m/Y'));
        $templateProcessor->setValue('{ANO}', date('Y'));
        $templateProcessor->setValue('{IDENTIFICADOR}', $usuario->tipo_id." ".$usuario->identificador);
        $templateProcessor->setValue('{ID}', $id);
        $templateProcessor->setValue('{CONCEPTO}', $usuario->estado == 'entregado' ? 'FAVORABLE' : 'NO FAVORABLE');

        // Guardar archivo temporalmente
        $tempWordFile = storage_path('app/public/documento_generado.docx');
        $templateProcessor->saveAs($tempWordFile);
        //$templateProcessor->saveAs(storage_path('app/public/debug_documento.docx'));


        return response()->download($tempWordFile)->deleteFileAfterSend(true);
    }
}
