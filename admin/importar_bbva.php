<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/init.php'; //
require HEAD;
require MENU;
require_once __DIR__ . '/../config.php';
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
    $ticket = trim($data[0] ?? '');
    $fecha_inicio = trim($data[1] ?? '');
    $vim = trim($data[2] ?? '');
    $afiliacion = trim($data[3] ?? '');
    $servicio = trim($data[4] ?? '');
    $proveedor = trim($data[5] ?? '');
    $temporal = trim($data[6] ?? '');
    $comercio = trim($data[7] ?? '');
    $domicilio = trim($data[8] ?? '');
    $colonia = trim($data[9] ?? '');
    $ciudad = trim($data[10] ?? '');
    $cp = trim($data[11] ?? '');
    $dar = trim($data[12] ?? '');
    $plaza = trim($data[13] ?? '');
    $asignacion_tedisa = trim($data[14] ?? '');
    $idc = trim($data[15] ?? '');
    $grupo = trim($data[16] ?? '');
    $cadena = trim($data[17] ?? '');
    $solicito = trim($data[18] ?? '');
    $telefono_contacto_1 = trim($data[19] ?? '');
    $tipo_tpv = trim($data[20] ?? '');
    $gestor = trim($data[21] ?? '');
    $email = trim($data[22] ?? '');
    $lada_tel = trim($data[23] ?? '');
    $referencia = trim($data[24] ?? '');
    $horario = trim($data[25] ?? '');
    $representante_domicilio_temporal = trim($data[26] ?? '');
    $telefono_temporal_1 = trim($data[27] ?? '');
    $comentarios = trim($data[28] ?? '');
    $fecha_limite = trim($data[29] ?? '');
    $fecha_cierre = trim($data[30] ?? '');
    $fecha_atencion = trim($data[31] ?? '');
    $hora_atencion = trim($data[32] ?? '');
    $fecha_cierre_telecarga = trim($data[33] ?? '');
    $solucion = trim($data[34] ?? '');
    $conclusion = trim($data[35] ?? '');
    $nivel_servicio = trim($data[36] ?? '');
    $tecnico = trim($data[37] ?? '');
    $recibio = trim($data[38] ?? '');
    $causa_fuera_tiempo = trim($data[39] ?? '');
    $sucursal = trim($data[40] ?? '');
    $division = trim($data[41] ?? '');
    $banca = trim($data[42] ?? '');
    $origen = trim($data[43] ?? '');
    $sistema = trim($data[44] ?? '');
    $ultima_modificacion = trim($data[45] ?? '');
    $tipo_ticket = trim($data[46] ?? '');
    $ultimo_usuario = trim($data[47] ?? '');
    $dar_2 = trim($data[48] ?? '');
    $giro = trim($data[49] ?? '');
    $estado = trim($data[50] ?? '');
    $tipo_servicio = trim($data[51] ?? '');
    $cantidad_insumos = trim($data[52] ?? '');
    $folio_telecarga = trim($data[53] ?? '');
    $almacen = trim($data[54] ?? '');
    $serie_asignada = trim($data[55] ?? '');
    $serie_instalada = trim($data[56] ?? '');
    $serie_retiro = trim($data[57] ?? '');
    $check_cierre_telecarga = trim($data[58] ?? '');
    $celular_contacto = trim($data[59] ?? '');
    $telefono_contacto_2 = trim($data[60] ?? '');
    $multimerchant = trim($data[61] ?? '');
    $vim_2 = trim($data[62] ?? '');
    $tipo_domicilio = trim($data[63] ?? '');

    try {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO servicios_bbva (
                ticket, fecha_inicio, vim, afiliacion, servicio, proveedor, temporal, comercio,
                domicilio, colonia, ciudad, cp, dar, plaza, asignacion_tedisa, idc, grupo, cadena, solicito,
                telefono_contacto_1, tipo_tpv, gestor, email, lada_tel, referencia, horario,
                representante_domicilio_temporal, telefono_temporal_1, comentarios, fecha_limite,
                fecha_cierre, fecha_atencion, hora_atencion, fecha_cierre_telecarga, solucion, conclusion,
                nivel_servicio, tecnico, recibio, causa_fuera_tiempo, sucursal, division, banca, origen,
                sistema, ultima_modificacion, tipo_ticket, ultimo_usuario, dar_2, giro, estado, tipo_servicio,
                cantidad_insumos, folio_telecarga, almacen, serie_asignada, serie_instalada, serie_retiro,
                check_cierre_telecarga, celular_contacto, telefono_contacto_2, multimerchant, vim_2, tipo_domicilio
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([
            $ticket, $fecha_inicio, $vim, $afiliacion, $servicio, $proveedor, $temporal, $comercio,
            $domicilio, $colonia, $ciudad, $cp, $dar, $plaza, $asignacion_tedisa, $idc, $grupo, $cadena, $solicito,
            $telefono_contacto_1, $tipo_tpv, $gestor, $email, $lada_tel, $referencia, $horario,
            $representante_domicilio_temporal, $telefono_temporal_1, $comentarios, $fecha_limite,
            $fecha_cierre, $fecha_atencion, $hora_atencion, $fecha_cierre_telecarga, $solucion, $conclusion,
            $nivel_servicio, $tecnico, $recibio, $causa_fuera_tiempo, $sucursal, $division, $banca, $origen,
            $sistema, $ultima_modificacion, $tipo_ticket, $ultimo_usuario, $dar_2, $giro, $estado, $tipo_servicio,
            $cantidad_insumos, $folio_telecarga, $almacen, $serie_asignada, $serie_instalada, $serie_retiro,
            $check_cierre_telecarga, $celular_contacto, $telefono_contacto_2, $multimerchant, $vim_2, $tipo_domicilio
        ])) {
            $insertados++;
        } else {
            $errores++;
        }
    } catch (Exception $e) {
        $errores++;
    }
}
?>

<?php
if (!defined('HEAD') || !defined('MENU') || !defined('FOOT')) {
    require_once dirname(__DIR__) . '/config.php';
}
require HEAD;
require MENU;
?>

<div class="main-content">
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

        </div>
      </div>

    </div>
  </div>

</div>

<form method="POST" action="migrar_a_omnipos.php">
  <button type="submit" class="btn btn-success btn-lg mt-3">🚀 Copiar a Servicios OMNIPOS</button>
</form>

</div>
<?php require FOOT; ?>
