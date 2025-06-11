<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function cargar_a_staging($archivoTmp, $extension, $banco, $pdo)
{
    $insertados = 0;
    $errores = 0;

    $tabla = "servicios_" . strtolower($banco);
    $columnasDestino = obtener_columnas($pdo, $tabla);

    if ($extension === 'csv') {
        $handle = fopen($archivoTmp, "r");
        if ($handle !== FALSE) {
            fgetcsv($handle, 1000, ","); // ignorar encabezado

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $fila = [];
                foreach ($columnasDestino as $index => $columnaBD) {
                    if (isset($data[$index])) {
                        $fila[$columnaBD] = $data[$index];
                    }
                }

                if (!empty($fila['ticket']) && !ticket_duplicado($fila['ticket'], $pdo, $tabla)) {
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
            $fila = [];
            foreach ($columnasDestino as $index => $columnaBD) {
                if (isset($data[$index])) {
                    $fila[$columnaBD] = $data[$index];
                }
            }

            if (!empty($fila['ticket']) && !ticket_duplicado($fila['ticket'], $pdo, $tabla)) {
                insertar_fila_directa($pdo, $tabla, $fila);
                $insertados++;
            } else {
                $errores++;
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
