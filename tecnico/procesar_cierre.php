<?php
// procesar_cierre.php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

// -----------------------------------------------------------------------------
// CONFIG / HELPERS
// -----------------------------------------------------------------------------
require_once __DIR__ . '/init.php/';   // Debe crear $pdo (PDO, UTF8, ERRMODE_EXCEPTION)
date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'msg' => 'No autorizado.']);
  exit;
}

$userId      = (int) $_SESSION['user_id'];
$usuario     = $_SESSION['nombre']    ?? 'N/A';
$usuarioIDC  = $_SESSION['idc']       ?? ($_SESSION['username'] ?? ''); // Ajusta según tu login
$roles       = $_SESSION['roles']     ?? '';

// Solo técnicos/IDCs cierran desde campo (ajusta si procede)
if (!preg_match('/\b(idc|tecnico)\b/i', $roles)) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'msg' => 'Permiso denegado.']);
  exit;
}

// Sanitizar helpers sencillos
function s($v){ return is_string($v) ? trim($v) : $v; }

// Carpeta base para evidencias
define('CIERRES_UPLOAD_DIR', __DIR__ . '/../uploads/cierres');

// Límite 1MB
define('MAX_IMG_BYTES', 1024 * 1024);

// Extensiones/MIME permitidos
$MIME_OK = [
  'image/jpeg' => 'jpg',
  'image/png'  => 'png',
  'image/webp' => 'webp'
];

// -----------------------------------------------------------------------------
// INPUT
// -----------------------------------------------------------------------------
$ticket              = s($_POST['ticket']              ?? '');
$atiende             = s($_POST['atiende']             ?? '');
$resultado           = s($_POST['resultado']           ?? ''); // 'Éxito' | 'Rechazo'
$serie_instalada     = s($_POST['serie_instalada']     ?? '');
$serie_retirada      = s($_POST['serie_retirada']      ?? '');
$solucion            = s($_POST['solucion']            ?? '');
$solucion_especifica = s($_POST['solucion_especifica'] ?? '');
$motivo_rechazo      = s($_POST['motivo_rechazo']      ?? '');
$observaciones       = s($_POST['observaciones']       ?? '');
$latitud             = s($_POST['latitud']             ?? '');
$longitud            = s($_POST['longitud']            ?? '');

// Archivos esperados (opcionales según resultado)
$filesMap = [
  'img_fachada'          => 'fachada',
  'img_hs'               => 'hoja_servicio',
  'img_serie_instalada'  => 'serie_instalada',
  'img_serie_retirada'   => 'serie_retirada',
];

// Validaciones básicas
if ($ticket === '' || $resultado === '') {
  echo json_encode(['ok' => false, 'msg' => 'Faltan campos obligatorios (ticket/resultado).']);
  exit;
}

$resultado = mb_strtolower($resultado);
if (!in_array($resultado, ['éxito','exito','rechazo'], true)) {
  echo json_encode(['ok' => false, 'msg' => 'Resultado inválido.']);
  exit;
}
$isExito = in_array($resultado, ['éxito','exito'], true);

// Geolocalización: opcional pero si llega, valida rango
if ($latitud !== '' && !is_numeric($latitud))   $latitud = null;
if ($longitud !== '' && !is_numeric($longitud)) $longitud = null;

// -----------------------------------------------------------------------------
// FUNC: Guardar archivo
// -----------------------------------------------------------------------------
function saveUpload(string $field, string $ticket, array $MIME_OK): ?string {
  if (!isset($_FILES[$field]) || !is_uploaded_file($_FILES[$field]['tmp_name'])) return null;

  $f = $_FILES[$field];
  if ($f['error'] !== UPLOAD_ERR_OK) {
    throw new RuntimeException("Error al subir archivo {$field}.");
  }
  if ($f['size'] > MAX_IMG_BYTES) {
    throw new RuntimeException("La imagen {$field} excede 1MB.");
  }

  // Verifica MIME real
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime  = $finfo->file($f['tmp_name']);
  if (!isset($MIME_OK[$mime])) {
    throw new RuntimeException("Formato no permitido en {$field}.");
  }
  $ext = $MIME_OK[$mime];

  // Carpeta: /uploads/cierres/YYYY/MM/ticket/
  $yy = date('Y');
  $mm = date('m');
  $dir = CIERRES_UPLOAD_DIR . "/{$yy}/{$mm}/" . preg_replace('/[^A-Za-z0-9_-]/','_', $ticket);
  if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
    throw new RuntimeException("No se pudo crear carpeta de evidencias.");
  }

  $name = $field . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
  $path = $dir . '/' . $name;

  if (!move_uploaded_file($f['tmp_name'], $path)) {
    throw new RuntimeException("No se pudo guardar {$field}.");
  }

  // Retorna ruta relativa (para mostrar/descargar desde web)
  $rel = str_replace(__DIR__ . '/..', '', $path);
  return $rel;
}

// -----------------------------------------------------------------------------
// VALIDACIONES DE NEGOCIO
// -----------------------------------------------------------------------------
try {
  // 1) Servicio debe existir, estar EN RUTA y asignado al técnico actual
  $sql = "SELECT id, ticket, estatus, idc, tecnico_id, fecha_limite
          FROM servicios_omnipos
          WHERE ticket = :t
          LIMIT 1";
  $st = $pdo->prepare($sql);
  $st->execute([':t' => $ticket]);
  $svc = $st->fetch(PDO::FETCH_ASSOC);

  if (!$svc) {
    echo json_encode(['ok' => false, 'msg' => 'El ticket no existe.']);
    exit;
  }
  if (strcasecmp($svc['estatus'] ?? '', 'En Ruta') !== 0) {
    echo json_encode(['ok' => false, 'msg' => 'El ticket no está en estatus En Ruta.']);
    exit;
  }

  // Verificación de asignación al técnico
  $asignadoOk = false;
  if ($usuarioIDC !== '') {
    if (isset($svc['idc']) && $svc['idc'] !== null && strcasecmp($svc['idc'], $usuarioIDC) === 0) $asignadoOk = true;
  }
  if (!$asignadoOk && !empty($svc['tecnico_id']) && (int)$svc['tecnico_id'] === $userId) $asignadoOk = true;

  if (!$asignadoOk) {
    echo json_encode(['ok' => false, 'msg' => 'El ticket no está asignado a tu usuario.']);
    exit;
  }

  // 2) Si ÉXITO → validar serie instalada
  if ($isExito) {
    if ($serie_instalada === '') {
      echo json_encode(['ok' => false, 'msg' => 'Captura la serie instalada.']);
      exit;
    }
    // Debe existir, estado=Asignado y tecnico_actual = IDC en sesión
    $sql = "SELECT id, serie, estado, tecnico_actual
            FROM inventario_tpv
            WHERE serie = :s
            LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([':s' => $serie_instalada]);
    $inv = $st->fetch(PDO::FETCH_ASSOC);

    if (!$inv || strcasecmp($inv['estado'] ?? '', 'Asignado') !== 0 || strcasecmp($inv['tecnico_actual'] ?? '', $usuarioIDC) !== 0) {
      echo json_encode(['ok' => false, 'msg' => '⚠️ Esta serie no está disponible en tu inventario.']);
      exit;
    }
  } else {
    // RECHAZO → motivo de rechazo requerido
    if ($motivo_rechazo === '') {
      echo json_encode(['ok' => false, 'msg' => 'Captura el motivo de rechazo.']);
      exit;
    }
  }

  // 3) Subidas de archivo (opcionales/condicionales)
  $rutas = [];
  foreach ($filesMap as $field => $alias) {
    if (isset($_FILES[$field]) && is_uploaded_file($_FILES[$field]['tmp_name'])) {
      $rutas[$alias] = saveUpload($field, $ticket, $MIME_OK); // puede lanzar excepción
    }
  }

  // ---------------------------------------------------------------------------
  // TRANSACCIÓN: insertar cierre + actualizar servicio (+ inventario si aplica)
  // ---------------------------------------------------------------------------
  $pdo->beginTransaction();

  // 3.1) Insert en cierres_servicio
  // Debes tener la tabla con estos campos (ajusta nombres si difieren)
  $sql = "INSERT INTO cierres_servicio
            (ticket, atiende, resultado, serie_instalada, serie_retirada,
             solucion, solucion_especifica, motivo_rechazo, observaciones,
             cerrado_por, cerrado_por_id, fecha_cierre, latitud, longitud,
             img_fachada, img_hoja_servicio, img_serie_instalada, img_serie_retirada)
          VALUES
            (:ticket, :atiende, :resultado, :serie_instalada, :serie_retirada,
             :solucion, :solucion_especifica, :motivo_rechazo, :observaciones,
             :cerrado_por, :cerrado_por_id, NOW(), :latitud, :longitud,
             :img_fachada, :img_hs, :img_si, :img_sr)";
  $st = $pdo->prepare($sql);
  $st->execute([
    ':ticket'              => $ticket,
    ':atiende'             => $atiende,
    ':resultado'           => $isExito ? 'Éxito' : 'Rechazo',
    ':serie_instalada'     => $isExito ? $serie_instalada : null,
    ':serie_retirada'      => $serie_retirada !== '' ? $serie_retirada : null,
    ':solucion'            => $isExito ? $solucion : null,
    ':solucion_especifica' => $isExito ? $solucion_especifica : null,
    ':motivo_rechazo'      => $isExito ? null : $motivo_rechazo,
    ':observaciones'       => $observaciones,
    ':cerrado_por'         => $usuario,
    ':cerrado_por_id'      => $userId,
    ':latitud'             => $latitud !== '' ? $latitud : null,
    ':longitud'            => $longitud !== '' ? $longitud : null,
    ':img_fachada'         => $rutas['fachada']         ?? null,
    ':img_hs'              => $rutas['hoja_servicio']   ?? null,
    ':img_si'              => $rutas['serie_instalada'] ?? null,
    ':img_sr'              => $rutas['serie_retirada']  ?? null,
  ]);

  // 3.2) Actualizar estado del servicio
  // Sugerencia: guarda también fecha_atencion/fecha_cierre y resultado
  $sql = "UPDATE servicios_omnipos
          SET estatus = 'Histórico',
              resultado = :res,
              fecha_atencion = NOW(),
              fecha_cierre   = NOW()
          WHERE ticket = :t";
  $st = $pdo->prepare($sql);
  $st->execute([
    ':res' => $isExito ? 'Éxito' : 'Rechazo',
    ':t'   => $ticket
  ]);

  // 3.3) Actualizaciones de inventario si Éxito
  if ($isExito) {
    // Serie instalada → Instalado
    $sql = "UPDATE inventario_tpv
            SET estado = 'Instalado'
            WHERE serie = :s LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([':s' => $serie_instalada]);

    // Log inventario (instalación)
    $sql = "INSERT INTO log_inventario (serie, tipo_movimiento, usuario, fecha, observaciones)
            VALUES (:serie, 'Instalación', :usuario, NOW(), :obs)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':serie'   => $serie_instalada,
      ':usuario' => $usuario,
      ':obs'     => "Ticket {$ticket} - Instalación"
    ]);

    // Serie retirada (si llega) → Retirado
    if ($serie_retirada !== '') {
      $sql = "UPDATE inventario_tpv
              SET estado = 'Retirado', tecnico_actual = :idc
              WHERE serie = :s LIMIT 1";
      $st = $pdo->prepare($sql);
      $st->execute([
        ':s'   => $serie_retirada,
        ':idc' => $usuarioIDC
      ]);

      // Log inventario (retiro)
      $sql = "INSERT INTO log_inventario (serie, tipo_movimiento, usuario, fecha, observaciones)
              VALUES (:serie, 'Retiro', :usuario, NOW(), :obs)";
      $st = $pdo->prepare($sql);
      $st->execute([
        ':serie'   => $serie_retirada,
        ':usuario' => $usuario,
        ':obs'     => "Ticket {$ticket} - Retiro"
      ]);
    }
  }

  $pdo->commit();

  // Para SLA/advertencia fuera de tiempo (opcional: solo informar)
  $fueraTiempo = false;
  if (!empty($svc['fecha_limite'])) {
    $lim = strtotime($svc['fecha_limite']);
    if ($lim && time() > $lim) $fueraTiempo = true;
  }

  echo json_encode([
    'ok' => true,
    'msg' => ($isExito ? 'Servicio cerrado con Éxito.' : 'Servicio cerrado en Rechazo.'),
    'ticket' => $ticket,
    'fuera_tiempo' => $fueraTiempo
  ]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  // Limpia archivos escritos si falla transacción (simple)
  // (Opcional: implementar registro de auditoría o eliminación exacta)
  http_response_code(500);
  echo json_encode(['ok' => false, 'msg' => 'Error al procesar el cierre', 'err' => $e->getMessage()]);
}
