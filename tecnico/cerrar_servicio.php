<?php
require_once __DIR__ . '/init.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ticket = $_GET['ticket'] ?? null;
$idc = $_SESSION['usuario_nombre'] ?? null;

// 🚫 Sin ticket
if (!$ticket) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded border border-red-300 font-semibold'>
        ❌ Ticket no proporcionado. Agrega ?ticket=XXXXXXXX en la URL.
        <div class='mt-2'><a href='/app/tecnico/' class='text-blue-600 underline'>← Volver</a></div>
    </div>";
    exit;
}

// 🚫 Sin técnico
if (!$idc) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded border border-red-300 font-semibold'>
        ⚠️ No hay técnico en sesión. Por favor vuelve a iniciar sesión.
        <div class='mt-2'><a href='/login.php' class='text-blue-600 underline'>Ir al login</a></div>
    </div>";
    exit;
}

// ✅ Validar servicio asignado
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket = ? AND estatus = 'En Ruta' AND idc = ?");
$stmt->execute([$ticket, $idc]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    echo "<div class='bg-yellow-100 text-yellow-800 p-4 rounded border border-yellow-300 font-semibold'>
        ❌ Este servicio no está asignado a ti o no está En Ruta.
        <div class='mt-2'><a href='/app/tecnico/' class='text-blue-600 underline'>← Volver</a></div>
    </div>";
    exit;
}

// ❌ Ya fue cerrado antes
$checkCierre = $pdo->prepare("SELECT id FROM cierres_servicio WHERE ticket = ?");
$checkCierre->execute([$ticket]);
if ($checkCierre->fetch()) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded border border-red-300 font-semibold'>
        ⚠️ Este servicio ya fue cerrado previamente.
        <div class='mt-2'><a href='/app/tecnico/' class='text-blue-600 underline'>← Volver</a></div>
    </div>";
    exit;
}

$stmtSol = $pdo->prepare("SELECT DISTINCT solucion FROM servicio_soluciones WHERE banco = ? AND servicio = ? AND activo = 1 ORDER BY solucion");
$stmtSol->execute([$servicio['banco'], $servicio['servicio']]);
$soluciones = $stmtSol->fetchAll(PDO::FETCH_COLUMN);
$motivos_rechazo = ['Local cerrado', 'No autorizado para recibir', 'Cliente canceló', 'Datos incorrectos'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar servicio <?= htmlspecialchars($ticket) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">
  <div class="max-w-lg mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">✅ Cerrar servicio</h1>
    <div id="resumen" class="bg-gray-50 rounded p-3 mb-4 text-sm space-y-1">
      <div><strong><?= htmlspecialchars($servicio['comercio']) ?></strong></div>
      <div>Servicio: <?= htmlspecialchars($servicio['servicio']) ?></div>
      <div id="resumen-valores"></div>
    </div>

    <form method="POST" action="procesar_cierre.php" class="space-y-4" enctype="multipart/form-data">
      <input type="hidden" name="ticket" value="<?= htmlspecialchars($ticket) ?>">
      <input type="hidden" name="latitud" id="latitud">
      <input type="hidden" name="longitud" id="longitud">

      <label class="block text-sm font-medium text-gray-700">
        Nombre de quien atiende (comercio):
        <input type="text" name="atiende" required autocomplete="off"
               class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. Luis Ramírez">
      </label>

      <label class="block text-sm font-medium text-gray-700">
        Resultado del servicio:
        <select name="resultado" id="resultado" required class="mt-1 w-full border rounded p-2 text-sm">
          <option value="">-- Seleccionar --</option>
          <option value="Éxito">✅ Éxito</option>
          <option value="Rechazo">❌ Rechazo</option>
        </select>
      </label>

      <div id="seccion_exito" class="space-y-4">
        <label class="block text-sm font-medium text-gray-700">
          Serie instalada:
          <input type="text" name="serie_instalada" inputmode="numeric" autocomplete="off"
                 class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. 123456">
        </label>

        <label class="block text-sm font-medium text-gray-700">
          Serie retirada:
          <input type="text" name="serie_retirada" inputmode="numeric" autocomplete="off"
                 class="mt-1 w-full border rounded p-2 text-sm" placeholder="Ej. 654321">
        </label>

        <label class="block text-sm font-medium text-gray-700">
          Solución:
          <select name="solucion" id="solucion" class="mt-1 w-full border rounded p-2 text-sm">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($soluciones as $s): ?>
              <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label class="block text-sm font-medium text-gray-700">
          Solución específica:
          <select name="solucion_especifica" id="solucion_especifica" class="mt-1 w-full border rounded p-2 text-sm">
            <option value="">-- Seleccionar --</option>
          </select>
        </label>
      </div>

      <div id="seccion_rechazo" class="space-y-4 hidden">
        <label class="block text-sm font-medium text-gray-700">
          Motivo de rechazo:
          <select name="motivo_rechazo" class="mt-1 w-full border rounded p-2 text-sm">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($motivos_rechazo as $m): ?>
              <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>

      <label class="block text-sm font-medium text-gray-700">
        Comentarios del técnico:
        <textarea name="observaciones" rows="3" class="mt-1 w-full border rounded p-2 text-sm"
                  placeholder="Notas adicionales..."></textarea>
      </label>

      <button type="submit"
              class="w-full bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">
        Guardar y cerrar servicio
      </button>
    </form>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const resultado = document.getElementById('resultado');
    const seccionExito = document.getElementById('seccion_exito');
    const seccionRechazo = document.getElementById('seccion_rechazo');
    const serieInst = document.querySelector('input[name="serie_instalada"]');
    const serieRet = document.querySelector('input[name="serie_retirada"]');
    const resumen = document.getElementById('resumen-valores');

    function actualizarVista() {
      const exito = resultado.value === 'Éxito';
      seccionExito.classList.toggle('hidden', !exito);
      seccionRechazo.classList.toggle('hidden', exito);
    }
    resultado.addEventListener('change', actualizarVista);
    actualizarVista();

    function actualizarResumen() {
      let html = '';
      if (serieInst.value) html += `<div>Serie instalada: <strong>${serieInst.value}</strong></div>`;
      if (serieRet.value) html += `<div>Serie retirada: <strong>${serieRet.value}</strong></div>`;
      resumen.innerHTML = html;
    }
    serieInst.addEventListener('input', actualizarResumen);
    serieRet.addEventListener('input', actualizarResumen);

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(pos => {
        document.getElementById('latitud').value = pos.coords.latitude.toFixed(6);
        document.getElementById('longitud').value = pos.coords.longitude.toFixed(6);
      });
    }

    document.getElementById('solucion').addEventListener('change', function() {
      const val = this.value;
      const selEsp = document.getElementById('solucion_especifica');
      selEsp.innerHTML = '<option value="">Cargando...</option>';
      if (!val) { selEsp.innerHTML = '<option value="">-- Seleccionar --</option>'; return; }
      fetch('ajax_soluciones.php?ticket=<?= urlencode($ticket) ?>&solucion=' + encodeURIComponent(val))
        .then(r => r.json())
        .then(data => {
          selEsp.innerHTML = '<option value="">-- Seleccionar --</option>' +
            data.map(v => `<option value="${v}">${v}</option>`).join('');
        }).catch(() => {
          selEsp.innerHTML = '<option value="">-- Seleccionar --</option>';
        });
    });
  });
  </script>
</body>
</html>
