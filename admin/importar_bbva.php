<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // NECESARIO para usar $_SESSION

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;


$insertados = 0;
$errores = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_csv'])) {
    $archivoTmp = $_FILES['archivo_csv']['tmp_name'];
    $nombreArchivo = $_FILES['archivo_csv']['name'];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

    if ($extension == 'csv') {
        if (is_uploaded_file($archivoTmp)) {
            $handle = fopen($archivoTmp, "r");
            if ($handle !== FALSE) {
                $headers = fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    procesarFila($data, $pdo, $insertados, $errores);
                }
                fclose($handle);
            } else {
                $errores++;
            }
        }
    } elseif ($extension == 'xlsx') {
        try {
            $spreadsheet = IOFactory::load($archivoTmp);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            array_shift($rows);

            foreach ($rows as $row) {
                procesarFila($row, $pdo, $insertados, $errores);
            }
        } catch (Exception $e) {
            $errores++;
        }
    } else {
        $errores++;
    }
} else {
    $errores++;
}

function procesarFila($data, $pdo, &$insertados, &$errores) {
    $campos = array_map(fn($v) => trim($v ?? ''), $data);

    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO servicios_bbva (...) VALUES (...)");
        if ($stmt->execute([...])) {
            $insertados++;
        } else {
            $errores++;
        }
    } catch (Exception $e) {
        $errores++;
    }
}

// Guardar resultado en sesión temporal y redirigir
$_SESSION['resultado_bbva'] = [
    'insertados' => $insertados,
    'errores'    => $errores
];

header("Location: resultado_bbva.php");
exit;
