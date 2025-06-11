<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
                $headers = fgetcsv($handle, 1000, ","); // Leer cabecera
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    procesarFila($data, $pdo, $insertados, $errores);
                }
                fclose($handle);
            } else {
                die("❌ Error al abrir el archivo CSV.");
            }
        }
    } elseif ($extension == 'xlsx') {
        try {
            $spreadsheet = IOFactory::load($archivoTmp);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            array_shift($rows); // Saltar cabecera

            foreach ($rows as $row) {
                procesarFila($row, $pdo, $insertados, $errores);
            }
        } catch (Exception $e) {
            die("❌ Error al leer el archivo Excel: " . $e->getMessage());
        }
    } else {
        die("❌ Formato de archivo no soportado. Sube un CSV o un Excel (.xlsx).");
    }
} else {
    die("❌ No se envió ningún archivo.");
}

// Función para procesar cada fila
function procesarFila($data, $pdo, &$insertados, &$errores) {
    // Aquí va el mapeo tal como lo tenías para servicios_bbva con los 64 campos
    // ...
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultado de la Carga BBVA - OMNIPOS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg">
        <div class="card-body text-center">
          
          <h3 class="card-title mb-4 text-primary">🔵 Resultado de la Carga BBVA</h3>
          
          <div class="alert alert-success">
            ✅ <strong><?php echo $insertados; ?></strong> registros insertados correctamente.
          </div>

          <?php if ($errores > 0): ?>
          <div class="alert alert-danger">
            ❌ <strong><?php echo $errores; ?></strong> errores al intentar insertar registros.
          </div>
          <?php endif; ?>

          <div class="mt-4">
            <a href="carga_bbva.php" class="btn btn-primary">⬅️ Subir otro archivo</a>
            <a href="carga.php" class="btn btn-secondary">🏠 Volver al Menú de Carga</a>
          </div>

          <form method="POST" action="migrar_a_omnipos.php" class="mt-4">
            <button type="submit" class="btn btn-success btn-lg">🚀 Copiar a Servicios OMNIPOS</button>
          </form>

        </div>
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
