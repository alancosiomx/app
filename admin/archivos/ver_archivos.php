<?php
require_once __DIR__ . '/../init.php';

// Leer carpetas dentro de /archivos_tecnicos
$base_path = __DIR__ . '/../../archivos_tecnicos';
$tecnicos = is_dir($base_path) ? array_filter(scandir($base_path), fn($d) => $d !== '.' && $d !== '..' && is_dir("$base_path/$d")) : [];
?>

<h2 class="text-2xl font-bold mb-4">📂 Archivos por técnico</h2>

<?php if (empty($tecnicos)): ?>
  <p class="text-gray-500">No hay archivos cargados aún.</p>
<?php else: ?>
  <?php foreach ($tecnicos as $idc): ?>
    <div class="mb-6">
      <h3 class="text-lg font-semibold mb-2">👨‍🔧 <?= htmlspecialchars($idc) ?></h3>
      <ul class="space-y-2 pl-4">
        <?php
          $ruta = "$base_path/$idc";
          foreach (scandir($ruta) as $archivo) {
              if ($archivo === '.' || $archivo === '..') continue;
              $url = "/archivos_tecnicos/" . urlencode($idc) . "/" . urlencode($archivo);
              $fecha = date('d M Y H:i', filemtime("$ruta/$archivo"));
              echo "<li class='text-sm'><a href='$url' target='_blank' class='text-blue-600 hover:underline'>📄 $archivo</a> <span class='text-gray-400'>($fecha)</span></li>";
          }
        ?>
      </ul>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
