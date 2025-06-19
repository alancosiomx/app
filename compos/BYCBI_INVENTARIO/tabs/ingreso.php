<?php
// Incluir la conexión a la base de datos de WordPress
include_once __DIR__ . '/../db.php';

// Establecer la zona horaria de Ciudad de México
date_default_timezone_set('America/Mexico_City');

// Función para agregar una terminal a la base de datos
function agregarTerminal($data) {
    global $wpdb;
    $table_name = 'pos_terminals'; // Asegúrate de que esta tabla existe en la base de datos

    // Definir los valores permitidos para el campo ENUM de status dentro de la función
    $status_permitidos = ['DISPONIBLE', 'DAÑADA', 'INSTALADA', 'DEVUELTA'];

    // Calcular short_serial solo si el modelo es MOVE2500
    $short_serial = ($data['model'] === 'MOVE2500') ? substr($data['serial_number'], -16) : '';

    // Verificar si la serie ya existe en la base de datos
    $existing_serial = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE serial_number = %s", $data['serial_number']));

    if ($existing_serial > 0) {
        // Mostrar mensaje si la serie ya existe
        echo '<p>Error: La serie ingresada (' . esc_html($data['serial_number']) . ') ya está registrada. No se permiten duplicados.</p>';
        return false;
    }

    // Verificar que el valor de status sea uno de los permitidos
    if (!in_array($data['status'], $status_permitidos)) {
        echo '<p>Error: El estatus ingresado no es válido. Debe ser uno de los siguientes: ' . implode(', ', $status_permitidos) . '</p>';
        return false;
    }

    // Obtener la fecha y hora actual en la zona horaria de Ciudad de México
    $entry_date = date('Y-m-d H:i:s'); // Formato MySQL para la fecha y hora actual

    // Preparar los datos para insertar en la base de datos
    $insert_data = [
        'bank' => sanitize_text_field($data['bank']),
        'brand' => sanitize_text_field($data['brand']),
        'model' => sanitize_text_field($data['model']),
        'serial_number' => sanitize_text_field($data['serial_number']),
        'short_serial' => sanitize_text_field($short_serial),
        'status' => sanitize_text_field($data['status']),
        'custodia' => 'ALMACEN', // Configurar la custodia como ALMACEN
        'entry_date' => $entry_date // Usar la fecha y hora ajustada
    ];

    // Insertar los datos en la base de datos y verificar si fue exitoso
    $result = $wpdb->insert($table_name, $insert_data);

    if ($result === false) {
        // Manejo de errores: Mostrar un mensaje en caso de falla
        echo '<p>Error al agregar la terminal (' . esc_html($data['serial_number']) . '): ' . esc_html($wpdb->last_error) . '</p>';
        return false;
    }

    return true;
}

// Manejar el envío del formulario para múltiples terminales
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['series'])) {
    $series = explode("\n", trim($_POST['series']));
    $constantData = [
        'bank' => $_POST['bank'],
        'brand' => $_POST['brand'],
        'model' => $_POST['model'],
        'status' => $_POST['status'] // Verificamos que el estatus esté siendo recibido
    ];

    $exitosas = 0; // Contador para terminales agregadas exitosamente
    $fallidas = 0; // Contador para terminales con errores

    foreach ($series as $serial) {
        $serial = trim($serial); // Eliminar espacios en blanco
        if ($serial !== '') {
            $terminalData = array_merge($constantData, ['serial_number' => $serial]);
            $resultado = agregarTerminal($terminalData);

            if ($resultado) {
                $exitosas++; // Incrementar el contador de éxito
            } else {
                $fallidas++; // Incrementar el contador de fallos
            }
        }
    }

    // Mostrar el resumen después del procesamiento
    echo '<p>Se procesaron ' . ($exitosas + $fallidas) . ' terminales.</p>';
    echo '<p>Terminales agregadas exitosamente: ' . $exitosas . '</p>';
    echo '<p>Terminales que fallaron: ' . $fallidas . '</p>';
}

// Definir las opciones para Banco, Marca y Modelo
$bancos = ['BANREGIO', 'BBVA'];
$marcas = [
    'INGENICO' => ['MOVE2500'],
    'PAX' => ['S920']
];

// Inicializar los valores seleccionados
$selected_banco = $_POST['bank'] ?? '';
$selected_marca = $_POST['brand'] ?? '';
$selected_modelo = $_POST['model'] ?? '';
$selected_status = $_POST['status'] ?? '';
?>

<h2>Ingreso Masivo de Terminales</h2>
<form method="POST">
    <!-- Selección de valores constantes -->
    <label for="bank">Banco:</label>
    <select name="bank" id="bank" required>
        <option value="">Seleccione un Banco</option>
        <?php foreach ($bancos as $banco): ?>
            <option value="<?= $banco ?>" <?= $selected_banco === $banco ? 'selected' : '' ?>><?= $banco ?></option>
        <?php endforeach; ?>
    </select>

    <label for="brand">Marca:</label>
    <select name="brand" id="brand" required onchange="updateModelOptions()">
        <option value="">Seleccione una Marca</option>
        <?php foreach ($marcas as $marca => $modelos): ?>
            <option value="<?= $marca ?>" <?= $selected_marca === $marca ? 'selected' : '' ?>><?= $marca ?></option>
        <?php endforeach; ?>
    </select>

    <label for="model">Modelo:</label>
    <select name="model" id="model" required>
        <option value="">Seleccione un Modelo</option>
        <?php 
        if (!empty($selected_marca) && isset($marcas[$selected_marca])) {
            foreach ($marcas[$selected_marca] as $modelo): ?>
                <option value="<?= $modelo ?>" <?= $selected_modelo === $modelo ? 'selected' : '' ?>><?= $modelo ?></option>
            <?php endforeach;
        }
        ?>
    </select>

    <label for="status">Estatus:</label>
    <select name="status" id="status" required>
        <option value="DISPONIBLE" <?= $selected_status === 'DISPONIBLE' ? 'selected' : '' ?>>Disponible</option>
        <option value="DAÑADA" <?= $selected_status === 'DAÑADA' ? 'selected' : '' ?>>Dañada</option>
        <option value="INSTALADA" <?= $selected_status === 'INSTALADA' ? 'selected' : '' ?>>Instalada</option>
        <option value="DEVUELTA" <?= $selected_status === 'DEVUELTA' ? 'selected' : '' ?>>Devuelta</option>
    </select>

    <!-- Campo para ingresar los números de serie -->
    <label for="series">Ingrese las series (una por línea):</label>
    <textarea name="series" id="series" rows="10" style="width: 100%;" placeholder="Ingrese una serie por línea" required></textarea>

    <button type="submit">Ingresar Terminales</button>
</form>

<script>
// Función para actualizar los modelos basados en la marca seleccionada
function updateModelOptions() {
    const brandSelect = document.getElementById('brand');
    const modelSelect = document.getElementById('model');
    const selectedBrand = brandSelect.value;

    // Limpiar opciones de modelo
    modelSelect.innerHTML = '<option value="">Seleccione un Modelo</option>';

    // Agregar opciones de modelo basadas en la marca seleccionada
    const modelos = <?= json_encode($marcas) ?>;
    if (selectedBrand in modelos) {
        modelos[selectedBrand].forEach(modelo => {
            const option = document.createElement('option');
            option.value = modelo;
            option.text = modelo;
            modelSelect.appendChild(option);
        });
    }
}
</script>
