<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DocumentoDTE;
use Illuminate\Support\Carbon;

class NotaCreditoController extends Controller
{
    public function emitirDesdeDTE(Request $request, DocumentoDTE $dte)
    {
        try {
            $original = json_decode(Storage::get($dte->json_legible_path), true);

            $fecha = now()->format('Y-m-d');
            $hora = now()->format('H:i:s');
            $codigoGeneracionNC = strtoupper(Str::uuid());

            // 1. Cuerpo del documento
            $cuerpo = $original['cuerpoDocumento'] ?? [];

            // 2. Resumen de la nota de crédito
            $totalGravada = $original['resumen']['totalGravada'] ?? 0;
            $totalIva = $original['resumen']['totalIva'] ?? 0;
            $totalPagar = $original['resumen']['totalPagar'] ?? 0;

            $resumen = [
                'totalNoSuj' => 0,
                'totalExenta' => 0,
                'totalGravada' => round($totalGravada, 2),
                'subTotalVentas' => round($totalGravada, 2),
                'descuNoSuj' => 0,
                'descuExenta' => 0,
                'descuGravada' => 0,
                'porcentajeDescuento' => 0,
                'totalDescu' => 0,
                'subTotal' => round($totalGravada, 2),
                'ivaRete1' => 0,
                'reteRenta' => 0,
                'montoTotalOperacion' => round($totalPagar, 2),
                'totalNoGravado' => 0,
                'totalPagar' => round($totalPagar, 2),
                'totalLetras' => $original['resumen']['totalLetras'],
                'saldoFavor' => 0,
                'condicionOperacion' => 1,
                'pagos' => [
                    [
                        "codigo" => "01",
                        "montoPago" => round($totalPagar, 2),
                        "referencia" => "0000",
                        "periodo" => null,
                        "plazo" => null
                    ]
                ],
                'tributos' => [
                    [
                        "codigo" => "20",
                        "descripcion" => "Impuesto al Valor Agregado 13%",
                        "valor" => round($totalIva, 2)
                    ]
                ],
                'numPagoElectronico' => null,
                'totalIva' => round($totalIva, 2)
            ];

            $nota = [
                'identificacion' => [
                    'version' => 1,
                    'ambiente' => '00',
                    'tipoDte' => '05',
                    'codigoGeneracion' => $codigoGeneracionNC,
                    'numeroControl' => "DTE-05-M001P001-" . strtoupper(Str::uuid()),
                    'tipoModelo' => 1,
                    'tipoOperacion' => 1,
                    'fecEmi' => $fecha,
                    'horEmi' => $hora,
                    'tipoMoneda' => 'USD',
                ],
                'emisor' => $original['emisor'],
                'receptor' => $original['receptor'],
                'documentoRelacionado' => [
                    [
                        'tipoDocumento' => $original['identificacion']['tipoDte'],
                        'tipoGeneracion' => 1,
                        'numeroGeneracion' => $original['identificacion']['codigoGeneracion'],
                        'numeroDocumento' => $original['identificacion']['numeroControl'],
                        'fechaEmision' => $original['identificacion']['fecEmi'],
                    ]
                ],
                'ventaTercero' => null,
                'cuerpoDocumento' => $cuerpo,
                'resumen' => $resumen,
                'extension' => null,
                'otrosDocumentos' => null,
                'apendice' => null,
            ];

            // Envío a API
            $payload = [
                "Usuario" => "02022504711049",
                "Password" => "Camioneta2025.",
                "Ambiente" => "00",
                "DteJson" => json_encode($nota),
                "Nit" => "008688551",
                "PasswordPrivado" => "Camioneta2025",
                "TipoDte" => '05',
                "CodigoGeneracion" => $codigoGeneracionNC,
                "NumControl" => $nota['identificacion']['numeroControl'],
                "VersionDte" => 1,
                "CorreoCliente" => $nota['receptor']['correo'] ?? null
            ];

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('http://98.89.90.33:7122/api/procesar-dte', $payload);

            if (!$response->successful()) {
                return back()->with('error', 'Error en envío de nota de crédito: ' . $response->body());
            }

            $respuestaAPI = $response->json();

            // Guardar en base de datos
            DocumentoDTE::create([
                'sello_recibido' => $respuestaAPI['selloRecibido'] ?? null,
                'codigo_generacion' => $codigoGeneracionNC,
                'numero_control' => $nota['identificacion']['numeroControl'],
                'factura' => $dte->factura,
                'fecha_generacion' => now(),
                'tipo_dte' => '05',
                'json_original_path' => "dtes_json/original_{$codigoGeneracionNC}.json",
                'json_legible_path' => "dtes_json/legible_{$codigoGeneracionNC}.json",
                'json_firmado_path' => null,
                'estado' => 'activo'
            ]);

            Storage::put("dtes_json/original_{$codigoGeneracionNC}.json", json_encode($nota, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            Storage::put("dtes_json/legible_{$codigoGeneracionNC}.json", json_encode($nota, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return back()->with('success', 'Nota de crédito emitida correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al emitir nota de crédito: ' . $e->getMessage());
        }
    }

    public function formEmitir(DocumentoDTE $dte)
{
    // Verificar que el DTE no esté anulado
    if ($dte->estado === 'anulado') {
        return redirect()->back()->with('error', 'No se puede emitir nota de crédito para un DTE anulado.');
    }

    // Verificar que el DTE sea de tipo válido para generar NC
    if (!in_array($dte->tipo_dte, ['01', '03'])) { // 01 = Consumidor Final, 03 = Crédito Fiscal
        return redirect()->back()->with('error', 'Solo se pueden generar notas de crédito para DTE tipo 01 o 03.');
    }

    return view('dtes.emitir', compact('dte'));
}
}
