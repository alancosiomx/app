<?php
// tecnico/cerrar_servicio.php
require_once __DIR__ . '/init.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ticket = $_GET['ticket'] ?? null;
$idc = $_SESSION['usuario_nombre'] ?? null;

// CSRF
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf'];

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

// ✅ Validar servicio asignado y en ruta
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

// Soluciones
$stmtSol = $pdo->prepare("
  SELECT DISTINCT solucion 
  FROM servicio_soluciones 
  WHERE banco = ? AND servicio = ? AND activo = 1 
  ORDER BY solucion
");
$stmtSol->execute([$servicio['banco'] ?? 'BBVA', $servicio['servicio'] ?? '']);
$soluciones = $stmtSol->fetchAll(PDO::FETCH_COLUMN);

// Inventario del técnico (series asignadas)
$stmtInv = $pdo->prepare("
  SELECT serie 
  FROM inventario_tpv 
  WHERE estado = 'Asignado' AND tecnico_actual = ?
  ORDER BY serie
");
$stmtInv->execute([$idc]);
$seriesAsignadas = $stmtInv->fetchAll(PDO::FETCH_COLUMN);

// Motivos rechazo
$motivos_rechazo = ['Local cerrado','No autorizado para recibir','Cliente canceló','Datos incorrectos','No localizado','Falta de insumos'];

// Para SLA visual
$fecha_limite = $servicio['fecha_limite'] ?? null;
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
  <div class="max-w-lg mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">✅ Cerrar servicio</h1>
    <div id="resumen" class="bg-gray-50 rounded p-3 mb-4 text-sm space-y-1 border">
      <div class="flex items-center justify-between">
        <div><strong><?= htmlspecialchars($servicio['comercio'] ?? '') ?></strong></div>
        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">Ticket <?= htmlspecialchars($ticket) ?></span>
      </div>
      <div>Servicio: <strong><?= htmlspecialchars($servicio['servicio'] ?? '') ?></strong></div>
      <?php if ($fecha_limite): ?>
        <div id="slaBadge" class="text-xs inline-block mt-1 px-2 py-0.5 rounded bg-gray-200">SLA: <?= htmlspecialchars($fecha_limite) ?></div>
      <?php endif; ?>
      <div id="resumen-valores" class="pt-2 border-t mt-2"></div>
    </div>

    <form method="POST" action="procesar_cierre.php" class="space-y-4" enctype="multipart/form-data" id="formCierre">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
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

      <!-- ÉXITO -->
      <div id="seccion_exito" class="space-y-4">
        <div class="grid grid-cols-1 gap-3">
          <label class="block text-sm font-medium text-gray-700">
            Serie instalada:
            <div class="flex gap-2 mt-1">
              <select name="serie_instalada" id="serie_instalada" class="flex-1 border rounded p-2 text-sm">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($seriesAsignadas as $serie): ?>
                  <option value="<?= htmlspecialchars($serie) ?>"><?= htmlspecialchars($serie) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="button" id="scanSerieInst" class="px-3 py-2 text-sm border rounded hover:bg-gray-50" title="Escanear código">
                📷
              </button>
            </div>
            <small class="text-gray-500">Se cargan las series asignadas a tu inventario. También puedes escanear.</small>
          </label>

          <label class="block text-sm font-medium text-gray-700">
            Serie retirada (opcional):
            <div class="flex gap-2 mt-1">
              <input type="text" name="serie_retirada" inputmode="numeric" autocomplete="off"
                     class="flex-1 border rounded p-2 text-sm" placeholder="Ej. 654321">
              <button type="button" id="scanSerieRet" class="px-3 py-2 text-sm border rounded hover:bg-gray-50" title="Escanear código">
                📷
              </button>
            </div>
          </label>
        </div>

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

        <!-- Evidencias -->
        <div class="grid grid-cols-1 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Foto de fachada (JPG/PNG ≤ 1MB)
              <input type="file" accept="image/*" capture="environment" name="foto_fachada" class="mt-1 w-full text-sm" />
            </label>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Hoja de servicio firmada (JPG/PNG ≤ 1MB)
              <input type="file" accept="image/*" capture="environment" name="foto_hs" class="mt-1 w-full text-sm" />
            </label>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Foto serie instalada (opcional, ≤ 1MB)
              <input type="file" accept="image/*" capture="environment" name="foto_serie_inst" class="mt-1 w-full text-sm" />
            </label>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Foto serie retirada (opcional, ≤ 1MB)
              <input type="file" accept="image/*" capture="environment" name="foto_serie_ret" class="mt-1 w-full text-sm" />
            </label>
          </div>
        </div>
      </div>

      <!-- RECHAZO -->
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

        <div class="grid grid-cols-1 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Foto evidencia (opcional, ≤ 1MB)
              <input type="file" accept="image/*" capture="environment" name="foto_rechazo" class="mt-1 w-full text-sm" />
            </label>
          </div>
        </div>
      </div>

      <label class="block text-sm font-medium text-gray-700">
        Comentarios del técnico:
        <textarea name="observaciones" rows="3" class="mt-1 w-full border rounded p-2 text-sm"
                  placeholder="Notas adicionales..."></textarea>
      </label>

      <button type="submit"
              class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
        Guardar y cerrar servicio
      </button>
    </form>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const resultado = document.getElementById('resultado');
  const seccionExito = document.getElementById('seccion_exito');
  const seccionRechazo = document.getElementById('seccion_rechazo');
  const serieInst = document.getElementById('serie_instalada');
  const serieRet = document.querySelector('input[name="serie_retirada"]');
  const resumen = document.getElementById('resumen-valores');
  const form = document.getElementById('formCierre');

  // SLA visual
  const slaBadge = document.getElementById('slaBadge');
  <?php if ($fecha_limite): ?>
    try {
      const lim = new Date('<?= addslashes($fecha_limite) ?>');
      const now = new Date();
      if (lim.toString() !== 'Invalid Date' && now > lim) {
        slaBadge.className = 'text-xs inline-block mt-1 px-2 py-0.5 rounded bg-red-100 text-red-700';
        slaBadge.textContent = '⚠️ Fuera de tiempo (SLA vencido) • <?= addslashes($fecha_limite) ?>';
      }
    } catch(e){}
  <?php endif; ?>

  function actualizarVista() {
    const exito = resultado.value === 'Éxito';
    seccionExito.classList.toggle('hidden', !exito);
    seccionRechazo.classList.toggle('hidden', exito);
  }
  resultado.addEventListener('change', actualizarVista);
  actualizarVista();

  function actualizarResumen() {
    let html = '';
    const atiendeVal = (document.querySelector('input[name="atiende"]').value||'').trim();
    const rs = resultado.value || '';
    if (atiendeVal) html += `<div>Atiende: <strong>${atiendeVal}</strong></div>`;
    if (rs) html += `<div>Resultado: <strong>${rs}</strong></div>`;
    if (serieInst.value) html += `<div>Serie instalada: <strong>${serieInst.value}</strong></div>`;
    if (serieRet.value) html += `<div>Serie retirada: <strong>${serieRet.value}</strong></div>`;
    resumen.innerHTML = html;
  }
  ['input','change'].forEach(evt => {
    document.querySelector('input[name="atiende"]').addEventListener(evt, actualizarResumen);
    resultado.addEventListener(evt, actualizarResumen);
    serieInst.addEventListener(evt, actualizarResumen);
    serieRet.addEventListener(evt, actualizarResumen);
  });
  actualizarResumen();

  // Geo
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      document.getElementById('latitud').value = pos.coords.latitude.toFixed(6);
      document.getElementById('longitud').value = pos.coords.longitude.toFixed(6);
    });
  }

  // Solución específica dependiente
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

  // Validar tamaño imágenes ≤ 1MB
  form.addEventListener('change', (e) => {
    if (e.target.type === 'file' && e.target.files[0]) {
      if (e.target.files[0].size > 1024 * 1024) {
        alert('La imagen supera 1MB. Por favor comprímela o toma otra foto.');
        e.target.value = '';
      }
    }
  });

  // Barcode/QR scan (si el navegador soporta BarcodeDetector)
  async function escanearYColocar(inputEl) {
    if ('BarcodeDetector' in window) {
      try {
        const detector = new BarcodeDetector({formats: ['qr_code','code_128','code_39','ean_13','ean_8','upc_a','upc_e']});
        // Captura via getUserMedia
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }});
        const video = document.createElement('video');
        video.srcObject = stream; await video.play();

        // Tomamos 2s de vista y tratamos de detectar cada 200ms
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const start = Date.now();
        let encontrado = '';
        while (Date.now() - start < 3000 && !encontrado) {
          canvas.width = video.videoWidth; canvas.height = video.videoHeight;
          ctx.drawImage(video, 0, 0);
          const bitmap = await createImageBitmap(canvas);
          const codes = await detector.detect(bitmap);
          if (codes && codes.length) {
            encontrado = codes[0].rawValue || '';
          } else {
            await new Promise(r => setTimeout(r, 200));
          }
        }
        stream.getTracks().forEach(t => t.stop());
        if (encontrado) {
          inputEl.value = encontrado;
          inputEl.dispatchEvent(new Event('change'));
          actualizarResumen();
        } else {
          alert('No se detectó ningún código. Intenta de nuevo o captura manualmente.');
        }
      } catch (err) {
        alert('No fue posible activar la cámara. Captura manualmente, por favor.');
      }
    } else {
      alert('Tu navegador no soporta escaneo nativo. Captura manualmente.');
    }
  }

  document.getElementById('scanSerieInst').addEventListener('click', () => escanearYColocar(serieInst));
  document.getElementById('scanSerieRet').addEventListener('click', () => escanearYColocar(serieRet));
});
</script>
</body>
</html>
