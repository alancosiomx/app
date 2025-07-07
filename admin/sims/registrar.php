<?php
// app/admin/sims/registrar.php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $series = $_POST['series'] ?? [];
    $marca = $_POST['marca'] ?? '';
    $banco = $_POST['banco'] ?? '';
    $tipo_sim = $_POST['tipo_sim'] ?? '';
    $usuario = $_SESSION['usuario_nombre'] ?? 'admin';

    $respuestas = [];

    foreach ($series as $serie_raw) {
        $serie = trim($serie_raw);
        if ($serie === '') continue;

        // Verifica duplicado
        $check = $conn->prepare("SELECT id FROM inventario_sims WHERE serie_sim = ?");
        $check->bind_param('s', $serie);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $respuestas[] = "❌ Serie duplicada: $serie";
            continue;
        }

        // Inserta en inventario
        $stmt = $conn->prepare("INSERT INTO inventario_sims (serie_sim, marca, banco, tipo_sim, estado, fecha_entrada, fecha_ultimo_movimiento) VALUES (?, ?, ?, ?, 'Disponible', CURDATE(), CURDATE())");
        $stmt->bind_param('ssss', $serie, $marca, $banco, $tipo_sim);
        if ($stmt->execute()) {
            // Logea el movimiento
            $log = $conn->prepare("INSERT INTO log_inventario_sims (serie_sim, tipo_movimiento, usuario, observaciones) VALUES (?, 'Recepción', ?, 'Alta manual')");
            $log->bind_param('ss', $serie, $usuario);
            $log->execute();

            $respuestas[] = "✅ Serie registrada: $serie";
        } else {
            $respuestas[] = "❌ Error al registrar: $serie";
        }
    }

    echo json_encode(['resultado' => $respuestas]);
    exit;
}
?>

<!-- HTML para formulario -->
<h2>📥 Registrar SIMs</h2>
<form method="POST" id="formAltaSIM">
    <label>Marca:</label>
    <input type="text" name="marca" required>

    <label>Banco (opcional):</label>
    <input type="text" name="banco">

    <label>Tipo SIM:</label>
    <select name="tipo_sim">
        <option value="">-- Seleccionar --</option>
        <option value="Normal">Normal</option>
        <option value="Nano">Nano</option>
        <option value="M2M">M2M</option>
    </select>

    <label>Series (una por línea):</label>
    <textarea name="series[]" rows="6" placeholder="89014103211118510720\n89014103211118510721"></textarea>

    <button type="submit">Registrar</button>
</form>

<div id="resultadoRegistro"></div>

<script>
document.getElementById('formAltaSIM').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = new FormData(this);
    const response = await fetch('registrar.php', { method: 'POST', body: form });
    const data = await response.json();
    document.getElementById('resultadoRegistro').innerHTML = data.resultado.map(r => `<p>${r}</p>`).join('');
});
</script>
