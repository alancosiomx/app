<?php
require_once __DIR__ . '/init.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ticket = $_GET['ticket'] ?? null;
$idc = $_SESSION['usuario_nombre'] ?? null;

if (!$ticket) { echo "<div class='text-red-600 font-bold'>❌ Ticket no proporcionado.</div>"; exit; }
if (!$idc)    { echo "<div class='text-red-600 font-bold'>⚠️ Sesión expirada.</div>"; exit; }

// Validar asignación
$stmt = $pdo->prepare("SELECT * FROM servicios_omnipos WHERE ticket=? AND estatus='En Ruta' AND idc=? LIMIT 1");
$stmt->execute([$ticket, $idc]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$servicio) { echo "<div class='text-red-600 font-bold'>❌ Servicio no disponible para cierre.</div>"; exit; }

// Evitar doble cierre
$chk = $pdo->prepare("SELECT 1 FROM cierres_servicio WHERE ticket=?");
$chk->execute([$ticket]);
if ($chk->fetchColumn()) { echo "<div class='text-amber-600 font-bold'>⚠️ Ya fue cerrado.</div>"; exit; }

// Cargar soluciones
$stmtSol = $pdo->prepare("SELECT DISTINCT solucion FROM servicio_soluciones WHERE banco=? AND servicio=? AND activo=1 ORDER BY solucion");
$stmtSol->execute([$servicio['banco'] ?? '', $servicio['servicio'] ?? '']);
$soluciones = $stmtSol->fetchAll(PDO::FETCH_COLUMN);

// Cargar series de inventario
$stmtInv = $pdo->prepare("SELECT serie FROM inventario_tpv WHERE estado='Asignado' AND tecnico_actual=? ORDER BY serie");
$stmtInv->execute([$idc]);
$series_inv = $stmtInv->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cerrar servicio <?= htmlspecialchars($ticket) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">

<h1 class="text-2xl font-bold mb-4">✅ Cerrar servicio</h1>
<div class="bg-gray-50 p-3 rounded mb-4 text-sm">
  <div><strong><?= htmlspecialchars($servicio['comercio']) ?></strong></div>
  <div>Servicio: <?= htmlspecialchars($servicio['servicio']) ?></div>
  <div>Ticket: <?= htmlspecialchars($ticket) ?></div>
</div>

<form method="POST" action="procesar_cierre.php" enctype="multipart/form-data" id="formCierre" class="space-y-4">
<input type="hidden" name="ticket" value="<?= htmlspecialchars($ticket) ?>">
<input type="hidden" name="latitud" id="latitud">
<input type="hidden" name="longitud" id="longitud">

<!-- Atiende -->
<label class="block">
  <span class="text-sm font-medium">Nombre de quien atiende:</span>
  <input type="text" name="atiende" required class="w-full border rounded p-2 mt-1" autocomplete="off">
</label>

<!-- Resultado -->
<label class="block">
  <span class="text-sm font-medium">Resultado:</span>
  <select name="resultado" id="resultado" required class="w-full border rounded p-2 mt-1">
    <option value="">-- Seleccionar --</option>
    <option value="Éxito">✅ Éxito</option>
    <option value="Rechazo">❌ Rechazo</option>
  </select>
</label>

<!-- Éxito -->
<div id="sec_exito" class="space-y-3">
  <label class="block">
    <span class="text-sm font-medium">Serie instalada:</span>
    <div class="flex gap-2">
      <select name="serie_instalada" class="flex-1 border rounded p-2">
        <option value="">-- Seleccionar --</option>
        <?php foreach ($series_inv as $serie): ?>
        <option value="<?= htmlspecialchars($serie) ?>"><?= htmlspecialchars($serie) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="button" id="scanSerie" class="bg-blue-600 text-white px-3 rounded">📷 Escanear</button>
    </div>
  </label>
  
  <label class="block">
    <span class="text-sm font-medium">Serie retirada:</span>
    <input type="text" name="serie_retirada" class="w-full border rounded p-2">
  </label>

  <label class="block">
    <span class="text-sm font-medium">Solución:</span>
    <select name="solucion" id="solucion" class="w-full border rounded p-2">
      <option value="">-- Seleccionar --</option>
      <?php foreach ($soluciones as $s): ?>
      <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label class="block">
    <span class="text-sm font-medium">Solución específica:</span>
    <select name="solucion_especifica" id="solucion_especifica" class="w-full border rounded p-2">
      <option value="">-- Seleccionar --</option>
    </select>
  </label>

  <label class="block">
    <span class="text-sm font-medium">Foto fachada (≤1MB):</span>
    <input type="file" name="foto_fachada" accept="image/*">
  </label>
  <label class="block">
    <span class="text-sm font-medium">Hoja de servicio firmada (≤1MB):</span>
    <input type="file" name="foto_hs" accept="image/*">
  </label>
</div>

<!-- Rechazo -->
<div id="sec_rechazo" class="space-y-3 hidden">
  <label class="block">
    <span class="text-sm font-medium">Motivo de rechazo:</span>
    <select name="motivo_rechazo" class="w-full border rounded p-2">
      <option value="">-- Seleccionar --</option>
      <option>Local cerrado</option>
      <option>No autorizado para recibir</option>
      <option>Cliente canceló</option>
      <option>Datos incorrectos</option>
    </select>
  </label>
  <label class="block">
    <span class="text-sm font-medium">Evidencia rechazo (≤1MB):</span>
    <input type="file" name="foto_rechazo" accept="image/*">
  </label>
</div>

<!-- Comentarios -->
<label class="block">
  <span class="text-sm font-medium">Comentarios:</span>
  <textarea name="observaciones" rows="3" class="w-full border rounded p-2"></textarea>
</label>

<button type="submit" class="w-full bg-green-600 text-white py-2 rounded">Guardar y cerrar</button>
</form>

<!-- Escáner QR -->
<div id="reader" class="mt-4 hidden"></div>

</div>

<script>
// Mostrar/ocultar secciones
const resultado = document.getElementById('resultado');
const secExito = document.getElementById('sec_exito');
const secRechazo = document.getElementById('sec_rechazo');
resultado.addEventListener('change', () => {
  const ex = resultado.value === 'Éxito';
  secExito.classList.toggle('hidden', !ex);
  secRechazo.classList.toggle('hidden', ex);
});

// Geolocalización
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(pos => {
    document.getElementById('latitud').value = pos.coords.latitude.toFixed(6);
    document.getElementById('longitud').value = pos.coords.longitude.toFixed(6);
  });
}

// Solución específica
document.getElementById('solucion').addEventListener('change', function(){
  const val = this.value, sel = document.getElementById('solucion_especifica');
  sel.innerHTML = '<option>Cargando...</option>';
  if (!val) { sel.innerHTML = '<option value="">-- Seleccionar --</option>'; return; }
  fetch('ajax_soluciones.php?ticket=<?= urlencode($ticket) ?>&solucion='+encodeURIComponent(val))
    .then(r=>r.json())
    .then(data => sel.innerHTML = '<option value="">-- Seleccionar --</option>'+data.map(v=>`<option>${v}</option>`).join(''))
    .catch(() => sel.innerHTML = '<option value="">-- Seleccionar --</option>');
});

// Compresión antes de enviar
document.getElementById('formCierre').addEventListener('change', e => {
  if (e.target.type === 'file' && e.target.files[0] && e.target.files[0].size > 1024*1024) {
    const file = e.target.files[0];
    const img = new Image();
    const reader = new FileReader();
    reader.onload = ev => {
      img.onload = () => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const scale = Math.min(800 / img.width, 800 / img.height);
        canvas.width = img.width * scale;
        canvas.height = img.height * scale;
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(blob => {
          const newFile = new File([blob], file.name, {type: 'image/jpeg'});
          const dt = new DataTransfer();
          dt.items.add(newFile);
          e.target.files = dt.files;
        }, 'image/jpeg', 0.7);
      };
      img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
  }
});

// Escáner
document.getElementById('scanSerie').addEventListener('click', () => {
  document.getElementById('reader').classList.remove('hidden');
  new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 })
    .render(decodedText => {
      document.querySelector('select[name="serie_instalada"]').value = decodedText;
      document.getElementById('reader').classList.add('hidden');
    });
});
</script>
</body>
</html>
