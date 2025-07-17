$ticket = $_GET['ticket'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
$stmt->execute([$ticket]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
  die("❌ Servicio no encontrado");
}

$afiliacion = $servicio['afiliacion'];

// Traer historial con misma afiliación (excepto el ticket actual)
$stmt_historial = $pdo->prepare("
  SELECT fecha_atencion, telefono_contacto_1, comentarios, horario
  FROM servicios_omnipos
  WHERE afiliacion = ? AND estatus = 'Concluido' AND ticket != ?
  ORDER BY fecha_atencion DESC
");
$stmt_historial->execute([$afiliacion, $ticket]);
$historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);
