<?php
// Función para mostrar el tab "Programar Cita"
function cbi_tab_programar_cita() {
    global $wpdb;

    ?>
    <h3>Programar Cita</h3>
    <p>Aquí podrás programar una cita para los servicios.</p>

    <form method="post">
        <label for="ticket">Ticket:</label>
        <input type="text" id="ticket" name="ticket" required>

        <label for="fecha_cita">Fecha de la cita:</label>
        <input type="date" id="fecha_cita" name="fecha_cita" required>

        <input type="submit" value="Programar Cita">
    </form>
    <?php

    // Procesar el formulario para programar la cita
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ticket = sanitize_text_field($_POST['ticket']);
        $fecha_cita = sanitize_text_field($_POST['fecha_cita']);

        if (!empty($ticket) && !empty($fecha_cita)) {
            // Actualizar el servicio en la base de datos
            $resultado = $wpdb->update(
                'servicios_omnipos',
                array(
                    'fecha_cita' => $fecha_cita,
                    'estatus' => 'Por Asignar'
                ),
                array('ticket' => $ticket)
            );

            if ($resultado !== false) {
                echo "<p style='color: #0073aa;'>Cita programada para el ticket <strong>{$ticket}</strong> el día <strong>{$fecha_cita}</strong>. El servicio está ahora 'Por Asignar'.</p>";
            } else {
                echo "<p style='color: red;'>Error al programar la cita. Por favor verifica el ticket.</p>";
            }
        } else {
            echo "<p style='color: red;'>Por favor, ingresa un ticket válido y una fecha de cita.</p>";
        }
    }
}	