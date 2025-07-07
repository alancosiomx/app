<?php
// app/admin/sims/asignar.php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $series = $_POST['series'] ?? [];
    $tecnico = $_POST['tecnico'] ?? '';
    $usuario = $_SESSION['usuario_nombre'] ?? 'admin';

    $respuestas = [];

    foreach ($series as $serie_raw) {
        $serie = trim($serie_raw);
        if ($serie === '') continue;

        // Verifica existencia
        $check = $conn->prepare("SELECT estado FROM inventario_sims WHERE serie_sim = ?");
        $check->bind_param('s', $serie);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $respuestas[] = "❌ No existe: $serie";
            continue;
        }

        $check->bind_result($estado);
        $check->fetch();

        if ($estado !== 'Disponible') {
            $respuestas[] = "⚠️ No disponible: $serie (estado: $estado)";
            continue;
        }

        // Asigna SIM
        $update = $conn->prepare("UPDATE inventario_sims SET estado = 'Asignada', tecnico_actual = ?, fecha_ultimo_movimiento = CURDATE() WHERE serie_sim = ?");
        $update->bind_param('ss', $tecnico, $serie);
        if ($update->execute()) {
            $log = $conn->prepare("INSERT INTO log_inventario_sims (serie_sim, tipo_movimiento, usuario, observaciones) VALUES (?, 'Asignación', ?, CONCAT('Asignada a ', ?))");
            $log->bind_param('sss', $serie, $usuario, $tecnico);
            $log->execute();

            $respuestas[] = "✅ Asignada: $serie";
        } else {
            $respuestas[] = "❌ Error al asignar: $serie";
        }
    }

    echo json_encode(['resultado' => $respuestas]);
    exit;
}
?>

<!-- HTML para formulario de asignación -->
<h2>👤 Asignar SIMs a Técnico</h2>
<form method="POST" id="formAsignarSIM">
    <label>Técnico:</label>
    <input type="text" name="tecnico" required>

    <label>Series a Asignar (una por línea):</label>
    <textarea name="series[]" rows="6" placeholder="89014103211118510720\n89014103211118510721"></textarea>

    <button type="submit">Asignar</button>
</form>

<div id="resultadoAsignacion"></div>

<script>
document.getElementById('formAsignarSIM').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = new FormData(this);
    const response = await fetch('asignar.php', { method: 'POST', body: form });
    const data = await response.json();
    document.getElementById('resultadoAsignacion').innerHTML = data.resultado.map(r => `<p>${r}</p>`).join('');
});
</script>
