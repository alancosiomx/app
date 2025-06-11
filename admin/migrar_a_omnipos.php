<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../config.php';

$insertados = 0;
$errores = 0;

function convertirFecha($fecha) {
    if (empty($fecha)) {
        return null;
    }
    $timestamp = strtotime($fecha);
    return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
}

try {
    $query = $pdo->query("SELECT * FROM servicios_bbva");
    $registros = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($registros as $row) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM servicios_omnipos WHERE ticket = ?");
        $check->execute([$row['ticket']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $pdo->prepare("
                INSERT INTO servicios_omnipos (
                    ticket, banco, fecha_inicio, vim, afiliacion, servicio, comercio, domicilio, colonia, ciudad, cp,
                    idc, solicito, telefono_contacto_1, tipo_tpv, referencia, horario, comentarios, fecha_limite, cantidad_insumos,
                    estatus, fecha_atencion, fecha_cita, resultado, id_usuario, observaciones, tecnico_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $row['ticket'], 'BBVA', convertirFecha($row['fecha_inicio']), $row['vim'], $row['afiliacion'],
                $row['servicio'], $row['comercio'], $row['domicilio'], $row['colonia'], $row['ciudad'], $row['cp'],
                $row['idc'], $row['solicito'], $row['telefono_contacto_1'], $row['tipo_tpv'], $row['referencia'],
                $row['horario'], $row['comentarios'], convertirFecha($row['fecha_limite']), $row['cantidad_insumos'],
                'Por Asignar', null, null, null, null, null, null
            ]);
            $insertados++;
        }
    }
} catch (Exception $e) {
    $errores++;
    die("❌ Error al migrar: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultado de Migración - OMNIPOS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg">
        <div class="card-body text-center">
          
          <h3 class="card-title mb-4 text-primary">🟢 Resultado de la Migración</h3>
          
          <div class="alert alert-success">
            ✅ <strong><?php echo $insertados; ?></strong> registros migrados a Servicios OMNIPOS.
          </div>

          <?php if ($errores > 0): ?>
          <div class="alert alert-danger">
            ❌ Hubo <strong><?php echo $errores; ?></strong> errores durante la migración.
          </div>
          <?php endif; ?>

          <div class="mt-4">
            <a href="carga_bbva.php" class="btn btn-primary">⬅️ Volver al Menú de Carga</a>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
