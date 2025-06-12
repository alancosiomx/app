<?php
require_once __DIR__ . '/../config.php';
session_start();

$nombre_tecnico = $_SESSION['usuario_nombre'] ?? '';
$success = null;
$errores = [];

// Obtener tickets asignados al técnico
$stmt = $pdo->prepare("SELECT ticket, servicio, banco FROM servicios_omnipos WHERE idc = ? AND estatus = 'En Ruta'");
$stmt->execute([$nombre_tecnico]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$datos = null;
$soluciones = [];

if (isset($_GET['ticket'])) {
    $ticket = $_GET['ticket'];

    $stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ?");
    $stmt->execute([$ticket]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($datos) {
        // Cargar soluciones generales
        $stmtSol = $pdo->prepare("SELECT DISTINCT solucion FROM servicio_soluciones WHERE banco = ? AND servicio = ? AND activo = 1");
        $stmtSol->execute([$datos['banco'], $datos['servicio']]);
        $soluciones = $stmtSol->fetchAll(PDO::FETCH_COLUMN);

        // Procesar cierre
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $_POST['resultado'] ?? '';
            $solucion = $_POST['solucion'] ?? '';
            $solucion_especifica = $_POST['solucion_especifica'] ?? '';
            $serie_instalada = $_POST['serie_instalada'] ?? '';
            $serie_retiro = $_POST['serie_retiro'] ?? '';
            $recibio = $_POST['recibio'] ?? '';
            $comentarios = $_POST['comentarios'] ?? '';
            $ahora = date('Y-m-d H:i:s');

            // Validaciones lógicas
            $solucion_lower = strtolower($solucion);
            $requiere_instalada = str_contains($solucion_lower, 'instalación') || str_contains($solucion_lower, 'reprogramación') || str_contains($solucion_lower, 'cambio') || str_contains($solucion_lower, 'sustitución');
            $requiere_retiro = str_contains($solucion_lower, 'cambio') || str_contains($solucion_lower, 'sustitución');

            if (empty($resultado) || empty($solucion) || empty($recibio)) {
                $errores[] = "Todos los campos obligatorios deben estar completos.";
            }

            if ($requiere_instalada && empty($serie_instalada)) {
                $errores[] = "La serie instalada es obligatoria para esta solución.";
            }

            if ($requiere_retiro && empty($serie_retiro)) {
                $errores[] = "La serie retirada es obligatoria para esta solución.";
            }

            // Verificación de serie instalada
            if ($requiere_instalada && $serie_instalada) {
                $stmt = $pdo->prepare("SELECT estado FROM inventario_tpv WHERE serie = ? AND tecnico_actual = ?");
                $stmt->execute([$serie_instalada, $nombre_tecnico]);
                $estado = $stmt->fetchColumn();

                if ($estado !== 'Asignado') {
                    $errores[] = "La serie instalada no está asignada a tu inventario.";
                }
            }

            if (empty($errores)) {
                // Actualizar servicio
                $stmt = $pdo->prepare("UPDATE servicios_omnipos SET 
                    resultado = ?, 
                    conclusion = ?, 
                    solucion = ?, 
                    solucion_especifica = ?, 
                    comentarios = ?, 
                    serie_instalada = ?, 
                    serie_retiro = ?, 
                    recibio = ?, 
                    estatus = 'Histórico', 
                    fecha_cierre = NOW() 
                    WHERE ticket = ?");
                $stmt->execute([
                    $resultado, $resultado, $solucion, $solucion_especifica,
                    $comentarios, $serie_instalada, $serie_retiro, $recibio, $ticket
                ]);

                // Inventario
                if ($requiere_instalada && $serie_instalada) {
                    $pdo->prepare("UPDATE inventario_tpv SET estado = 'Instalado' WHERE serie = ?")->execute([$serie_instalada]);
                }

                if ($requiere_retiro && $serie_retiro) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventario_tpv WHERE serie = ?");
                    $stmt->execute([$serie_retiro]);
                    $existe = $stmt->fetchColumn();

                    if ($existe) {
                        $pdo->prepare("UPDATE inventario_tpv SET estado = 'Dañado', tecnico_actual = ? WHERE serie = ?")->execute([$nombre_tecnico, $serie_retiro]);
                    } else {
                        $pdo->prepare("INSERT INTO inventario_tpv (serie, estado, tecnico_actual, banco, observaciones, fecha_entrada) VALUES (?, 'Dañado', ?, ?, ?, NOW())")
                            ->execute([$serie_retiro, $nombre_tecnico, $datos['banco'], "Serie retirada en servicio $ticket"]);
                    }
                }

                $success = "✅ Servicio cerrado correctamente.";
                unset($_GET['ticket']);
                unset($datos);
            }
        }
    }
}

$contenido = __FILE__;
include __DIR__ . '/layout_tecnico.php';
?>

<div class="p-4">
  <h2 class="text-lg font-bold mb-3">✅ Cerrar Servicio</h2>

  <?php if ($success): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4"><?= $success ?></div>
  <?php endif; ?>

  <?php if ($errores): ?>
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
      <?= implode('<br>', $errores) ?>
    </div>
  <?php endif; ?>

  <?php if (!$datos): ?>
    <form method="GET">
      <label class="block mb-1 font-medium">Selecciona un Ticket:</label>
      <select name="ticket" class="w-full border p-2 rounded mb-4" required>
        <option value="">-- Elige --</option>
        <?php foreach ($tickets as $t): ?>
          <option value="<?= $t['ticket'] ?>"><?= $t['ticket'] ?> (<?= $t['servicio'] ?> - <?= $t['banco'] ?>)</option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Continuar</button>
    </form>
  <?php else: ?>
    <form method="POST">
      <p><strong>Ticket:</strong> <?= htmlspecialchars($datos['ticket']) ?></p>
      <p><strong>Servicio:</strong> <?= htmlspecialchars($datos['servicio']) ?></p>
      <p><strong>Banco:</strong> <?= htmlspecialchars($datos['banco']) ?></p>

      <label class="block mt-4 mb-1 font-medium">Resultado:</label>
      <select name="resultado" required class="w-full border p-2 rounded mb-4">
        <option value="">-- Selecciona --</option>
        <option value="Exito">✅ Éxito</option>
        <option value="Rechazo">❌ Rechazo</option>
      </select>

      <label class="block mb-1 font-medium">Solución General:</label>
      <select name="solucion" id="solucion" class="w-full border p-2 rounded mb-4" required>
        <option value="">-- Selecciona --</option>
        <?php foreach ($soluciones as $sol): ?>
          <option value="<?= htmlspecialchars($sol) ?>"><?= htmlspecialchars($sol) ?></option>
        <?php endforeach; ?>
      </select>

      <label class="block mb-1 font-medium">Solución Específica:</label>
      <select name="solucion_especifica" id="solucion_especifica" class="w-full border p-2 rounded mb-4" required>
        <option value="">-- Carga desde solución general --</option>
      </select>

      <label class="block mb-1 font-medium">Serie Instalada:</label>
      <input type="text" name="serie_instalada" class="w-full border p-2 rounded mb-4" />

      <label class="block mb-1 font-medium">Serie Retiro:</label>
      <input type="text" name="serie_retiro" class="w-full border p-2 rounded mb-4" />

      <label class="block mb-1 font-medium">Recibió (Cliente):</label>
      <input type="text" name="recibio" class="w-full border p-2 rounded mb-4" required />

      <label class="block mb-1 font-medium">Comentarios:</label>
      <textarea name="comentarios" class="w-full border p-2 rounded mb-4" rows="3"></textarea>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Cerrar Servicio</button>
    </form>
  <?php endif; ?>
</div>

<script>
document.getElementById('solucion')?.addEventListener('change', function () {
  const solucion = this.value;
  const ticket = "<?= htmlspecialchars($_GET['ticket'] ?? '') ?>";

  fetch('ajax_soluciones.php?ticket=' + encodeURIComponent(ticket) + '&solucion=' + encodeURIComponent(solucion))
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('solucion_especifica');
      select.innerHTML = '<option value="">-- Selecciona --</option>';
      data.forEach(op => {
        const opt = document.createElement('option');
        opt.value = op;
        opt.textContent = op;
        select.appendChild(opt);
      });
    });
});
</script>
