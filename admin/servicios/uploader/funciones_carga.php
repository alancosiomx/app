<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function cargar_a_staging($archivoTmp, $extension, $banco, $pdo)
{
    $insertados = 0;
    $errores = 0;

    $tabla = "servicios_" . strtolower($banco);
    $columnasDestino = obtener_columnas($pdo, $tabla);
    $clave_index = ($tabla === 'servicios_banregio') ? 1 : 0;

    if ($extension === 'csv') {
        $handle = fopen($archivoTmp, "r");
        if ($handle !== FALSE) {
            fgetcsv($handle, 1000, ","); // ignorar encabezado

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $clave_valor = trim($data[$clave_index] ?? '');

                if (!empty($clave_valor) && !ticket_duplicado($clave_valor, $pdo, $tabla)) {
                    $fila = [];

                    foreach ($columnasDestino as $index => $columnaBD) {
                        if (isset($data[$index])) {
                            $valor = trim($data[$index]);

                            if (in_array($columnaBD, ['fecha_inicio', 'fecha_limite', 'fecha_atencion'])) {
                                $valor = normalizar_fecha($valor);
                            }

                            $fila[$columnaBD] = $valor !== '' ? $valor : null;
                        }
                    }

                    insertar_fila_directa($pdo, $tabla, $fila);
                    $insertados++;
                } else {
                    $errores++;
                }
            }
            fclose($handle);
        }
    } elseif ($extension === 'xlsx') {
        $spreadsheet = IOFactory::load($archivoTmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        array_shift($rows); // ignorar encabezado

        foreach ($rows as $data) {
            $clave_valor = trim($data[$clave_index] ?? '');

            if (!empty($clave_valor) && !ticket_duplicado($clave_valor, $pdo, $tabla)) {
                $fila = [];

                foreach ($columnasDestino as $index => $columnaBD) {
                    if (isset($data[$index])) {
                        $valor = trim($data[$index]);

                        if (in_array($columnaBD, ['fecha_inicio', 'fecha_limite', 'fecha_atencion'])) {
                            $valor = normalizar_fecha($valor);
                        }

                        $fila[$columnaBD] = $valor !== '' ? $valor : null;
                    }
                }

                insertar_fila_directa($pdo, $tabla, $fila);
                $insertados++;
            } else {
                $errores++;
            }
        }
    }

    return "✅ Se cargaron correctamente $insertados registros. ⚠️ Duplicados ignorados: $errores.";
}

function normalizar_fecha($fecha) {
    $fecha = trim($fecha);
    if (!$fecha) return null;

    $timestamp = strtotime(str_replace('/', '-', $fecha));
    if (!$timestamp || $timestamp === false) return null;

    return date('Y-m-d', $timestamp);
}


    return null;
}

function ticket_duplicado($valor, $pdo, $tabla)
{
    $columna = ($tabla === 'servicios_banregio') ? 'folio_cliente' : 'ticket';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $tabla WHERE $columna = ?");
    $stmt->execute([$valor]);
    return $stmt->fetchColumn() > 0;
}

function insertar_fila_directa($pdo, $tabla, $fila)
{
    $campos = array_keys($fila);
    $sql = "INSERT INTO $tabla (" . implode(',', $campos) . ") VALUES (:" . implode(', :', $campos) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($fila);
}

function obtener_columnas($pdo, $tabla)
{
    $stmt = $pdo->query("DESCRIBE $tabla");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
