<?php
require_once __DIR__ . '/../init.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /login.php");
    exit();
}

// Tabs
$tabs = [
    'por_asignar' => 'Por Asignar',
    'en_ruta'     => 'En Ruta',
    'concluido'   => 'Concluido',
    'citas'       => 'Citas',
];

$tab = $_GET['tab'] ?? 'por_asignar';

$contenido_tab = match($tab) {
    'en_ruta'   => __DIR__ . '/contenido_en_ruta.php',
    'concluido' => __DIR__ . '/contenido_concluido.php',
    'citas'     => __DIR__ . '/contenido_citas.php',
    default     => __DIR__ . '/contenido_por_asignar.php',
};

ob_start();
?>

<!-- Header -->
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

<!-- Panel de Alertas -->
<?php include __DIR__ . '/../includes/panel_alertas.php'; ?>

<!-- Contenido dinámico -->
<?php include $contenido_tab; ?>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';
