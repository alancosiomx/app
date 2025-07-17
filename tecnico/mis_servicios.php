require_once __DIR__ . '/../init.php';
if (!isset($pdo)) {
  die("❌ ERROR: Conexión PDO no inicializada");
}


$idc = $_SESSION['usuario_username'] ?? null;
$contenido = __DIR__ . '/bloques/mis_servicios_lista.php';

if (!$idc) {
  header("Location: /login.php");
  exit;
}

$stmt = $pdo->prepare("
  SELECT ticket, afiliacion, comercio, ciudad, fecha_atencion 
  FROM servicios_omnipos 
  WHERE idc = ? AND estatus = 'En Ruta' 
  ORDER BY fecha_atencion DESC
");
$stmt->execute([$idc]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<!-- 🟢 Llega antes de layout -->";
require_once __DIR__ . '/layout.php';
