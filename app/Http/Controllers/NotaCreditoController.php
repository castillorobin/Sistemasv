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

            // Permitir seleccionar si se emite nota total o parcial
            $montoParcial = floatval($request->input('monto', 0));
            $esTotal = $montoParcial === 0;

            $totalGravadaOriginal = floatval($original['resumen']['totalGravada'] ?? 0);
            $totalIvaOriginal = floatval($original['resumen']['tributos'][0]['valor'] ?? 0);
            $totalOriginal = $totalGravadaOriginal + $totalIvaOriginal;

            $base = $esTotal ? $totalGravadaOriginal : round($montoParcial / 1.13, 2);
            $iva = round($base * 0.13, 2);
            $total = $base + $iva;

            $nota = [
                'identificacion' => [
                    'version' => 3,
                    'ambiente' => '00',
                    'tipoDte' => '05',
                    'numeroControl' => 'DTE-05-B001P001-' . str_pad(mt_rand(1, 999999999999999), 15, '0', STR_PAD_LEFT),
                    'codigoGeneracion' => $codigoGeneracionNC,
                    'tipoModelo' => 1,
                    'tipoOperacion' => 1,
                    'tipoContingencia' => null,
                    'motivoContin' => null,
                    'fecEmi' => $fecha,
                    'horEmi' => $hora,
                    'tipoMoneda' => 'USD'
                ],
                'documentoRelacionado' => [[
                    'tipoDocumento' => $original['identificacion']['tipoDte'],
                    'tipoGeneracion' => 1,
                    'numeroDocumento' => $original['identificacion']['numeroControl'],
                    'fechaEmision' => $original['identificacion']['fecEmi'],
                ]],
                'emisor' => [
                    'nit' => $original['emisor']['nit'],
                    'nrc' => $original['emisor']['nrc'],
                    'nombre' => $original['emisor']['nombre'],
                    'codActividad' => $original['emisor']['codActividad'],
                    'descActividad' => $original['emisor']['descActividad'],
                    'nombreComercial' => $original['emisor']['nombreComercial'],
                    'tipoEstablecimiento' => $original['emisor']['tipoEstablecimiento'],
                    'direccion' => $original['emisor']['direccion'],
                    'telefono' => $original['emisor']['telefono'],
                    'correo' => $original['emisor']['correo']
                ],
                'receptor' => [
                    'nit' => $original['receptor']['nit'] ?? '00000000000000',
                    'nrc' => $original['receptor']['nrc'] ?? null,
                    'nombre' => $original['receptor']['nombre'] ?? 'CONSUMIDOR FINAL',
                    'codActividad' => $original['receptor']['codActividad'] ?? '000000',
                    'descActividad' => $original['receptor']['descActividad'] ?? 'NO APLICA',
                    'direccion' => $original['receptor']['direccion'] ?? [
                        'departamento' => 'SAN SALVADOR',
                        'municipio' => 'SAN SALVADOR',
                        'complemento' => 'NO DEFINIDO'
                    ],
                    'telefono' => $original['receptor']['telefono'] ?? '0000-0000',
                    'correo' => $original['receptor']['correo'] ?? 'correo@ejemplo.com',
                    'nombreComercial' => $original['receptor']['nombreComercial'] ?? 'CONSUMIDOR FINAL'
                ],
                'ventaTercero' => null,
                'cuerpoDocumento' => [[
                    'numItem' => 1,
                    'tipoItem' => 1,
                    'numeroDocumento' => null,
                    'cantidad' => 1,
                    'codigo' => 'NC01',
                    'uniMedida' => 59,
                    'descripcion' => $esTotal ? 'Nota de crédito por anulación total' : 'Nota de crédito parcial',
                    'precioUni' => -$base,
                    'montoDescu' => 0.00,
                    'ventaNoSuj' => 0.00,
                    'ventaExenta' => 0.00,
                    'ventaGravada' => -$base,
                    'tributos' => [[
                        'codigo' => '20',
                        'descripcion' => 'IVA',
                        'valor' => -$iva
                    ]]
                ]],
                'resumen' => [
                    'totalNoSuj' => 0.00,
                    'totalExenta' => 0.00,
                    'totalGravada' => -$base,
                    'subTotalVentas' => -$base,
                    'ivaRete1' => 0.00,
                    'reteRenta' => 0.00,
                    'montoTotalOperacion' => -$total,
                    'totalLetras' => 'MENOS ' . strtoupper($this->numeroALetras($total)),
                    'condicionOperacion' => 1,
                    'tributos' => [[
                        'codigo' => '20',
                        'descripcion' => 'IVA',
                        'valor' => -$iva
                    ]],
                ],
                'extension' => null,
                'apendice' => null
            ];

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
                "VersionDte" => 3,
                "CorreoCliente" => $nota['receptor']['correo'] ?? null
            ];

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post('http://98.89.90.33:7122/api/procesar-dte', $payload);

            if (!$response->successful()) {
                return back()->with('error', 'Error en envío de nota de crédito: ' . $response->body());
            }

            $respuestaAPI = $response->json();

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
        return view('dtes.emitir', compact('dte'));
    }

     private function numeroALetras($numero)
{
    $unidad = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince',
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte'
    ];

    $decenas = [
        '', '', 'veinti', 'treinta', 'cuarenta', 'cincuenta',
        'sesenta', 'setenta', 'ochenta', 'noventa'
    ];

    $centenas = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos',
        'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    if ($numero == 0) return 'Cero dólares 00/100';

    $entero = floor($numero);
    $centavos = round(($numero - $entero) * 100);

    $letras = '';

    if ($entero >= 1000000) {
        $millones = floor($entero / 1000000);
        $letras .= $this->numeroALetras($millones) . ' millón' . ($millones > 1 ? 'es' : '') . ' ';
        $entero %= 1000000;
    }

    if ($entero >= 1000) {
        $miles = floor($entero / 1000);
        if ($miles == 1) {
            $letras .= 'mil ';
        } else {
            $letras .= $this->numeroALetras($miles) . ' mil ';
        }
        $entero %= 1000;
    }

    if ($entero > 0) {
        if ($entero == 100) {
            $letras .= 'cien';
        } else {
            $c = floor($entero / 100);
            $d = floor(($entero % 100) / 10);
            $u = $entero % 10;

            $letras .= $centenas[$c];

            if ($d == 1 || ($d == 2 && $u == 0)) {
                $letras .= ($c > 0 ? ' ' : '') . $unidad[$d * 10 + $u];
            } elseif ($d == 2) {
                $letras .= 'i' . $unidad[$u];
            } elseif ($d > 2) {
                $letras .= ($c > 0 ? ' ' : '') . $decenas[$d];
                if ($u > 0) {
                    $letras .= ' y ' . $unidad[$u];
                }
            } elseif ($u > 0) {
                $letras .= ($c > 0 ? ' ' : '') . $unidad[$u];
            }
        }
    }

    $letras = trim(ucfirst($letras)) . ' dólares';
    $letras .= ' con ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';

    return $letras;
}
    
}

// Asegúrate de tener disponible la función "numeroALetras" en algún helper o controlador.
