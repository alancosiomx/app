<?php
require_once __DIR__ . '/../../init.php';

// Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Tabs disponibles
$tabs = [
    'por_asignar' => 'Por Asignar',
    'en_ruta'     => 'En Ruta',
    'concluido'   => 'Concluido',
    'citas'       => 'Citas',
];

// Detectar tab activo
$tab = $_GET['tab'] ?? 'por_asignar';
$contenido = match($tab) {
    'en_ruta'   => __DIR__ . '/contenido_en_ruta.php',
    'concluido' => __DIR__ . '/contenido_concluido.php',
    'citas'     => __DIR__ . '/contenido_citas.php',
    default     => __DIR__ . '/contenido_por_asignar.php',
};

ob_start();
?>

<!-- Encabezado de módulo -->
<div class="flex flex-wrap items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">📋 Servicios</h1>
    <div class="space-x-2">
        <a href="generar_hs.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">📄 Generar HS</a>
        <a href="exportar_excel.php?tab=<?= $tab ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">⬇️ Exportar</a>
    </div>
</div>

<!-- Tabs -->
<div class="flex space-x-2 mb-6">
    <?php foreach ($tabs as $key => $label): ?>
        <a href="?tab=<?= $key ?>"
           class="px-4 py-2 rounded-full border <?= $tab === $key ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Panel de Alertas (si aplica) -->
<?php include __DIR__ . '/includes/panel_alertas.php'; ?>

<!-- Contenido dinámico del tab -->
<?php include $contenido; ?>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';
