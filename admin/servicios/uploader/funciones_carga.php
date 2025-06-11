<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function cargar_a_staging($archivoTmp, $extension, $banco, $pdo)
{
    $insertados = 0;
    $errores = 0;

    // Obtener nombre de la tabla temporal
    $tabla = "servicios_" . strtolower($banco);

    if ($extension === 'csv') {
        $handle = fopen($archivoTmp, "r");
        if ($handle !== FALSE) {
            $headers = fgetcsv($handle, 1000, ",");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $fila = array_combine($headers, $data);
                if ($fila && isset($fila['Ticket'])) {
                    $ticket = trim($fila['Ticket']);
                    if (!ticket_duplicado($ticket, $pdo, $tabla)) {
                        insertar_fila($pdo, $tabla, $fila);
                        $insertados++;
                    } else {
                        $errores++;
                    }
                }
            }
            fclose($handle);
        }
    } elseif ($extension === 'xlsx') {
        $spreadsheet = IOFactory::load($archivoTmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $headers = array_shift($rows);
        foreach ($rows as $row) {
            $fila = [];
            foreach ($headers as $key => $campo) {
                $fila[$campo] = $row[$key] ?? '';
            }

            if (isset($fila['Ticket'])) {
                $ticket = trim($fila['Ticket']);
                if (!ticket_duplicado($ticket, $pdo, $tabla)) {
                    insertar_fila($pdo, $tabla, $fila);
                    $insertados++;
                } else {
                    $errores++;
                }
            }
        }
    }

    return "✅ Se cargaron correctamente $insertados registros. ⚠️ Duplicados ignorados: $errores.";
}

function ticket_duplicado($ticket, $pdo, $tabla)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $tabla WHERE ticket = ?");
    $stmt->execute([$ticket]);
    return $stmt->fetchColumn() > 0;
}

function insertar_fila($pdo, $tabla, $fila)
{
    // Mapear solo columnas válidas que existan en la tabla
    $columnasValidas = obtener_columnas($pdo, $tabla);
    $campos = [];
    $valores = [];

    foreach ($fila as $columna => $valor) {
        $col = strtolower(str_replace(' ', '_', trim($columna)));
        if (in_array($col, $columnasValidas)) {
            $campos[] = $col;
            $valores[$col] = $valor;
        }
    }

    if (count($campos)) {
        $sql = "INSERT INTO $tabla (" . implode(',', $campos) . ") VALUES (:" . implode(', :', $campos) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
    }
}

function obtener_columnas($pdo, $tabla)
{
    $stmt = $pdo->query("DESCRIBE $tabla");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
