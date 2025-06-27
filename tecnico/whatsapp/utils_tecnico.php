// /app/tecnico/whatsapp/utils_tecnico.php
<?php
// Aquí van funciones comunes, por ejemplo:
function getIdcByTelefono($telefono) {
    require('../../../config/database.php');
    $stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE telefono_whatsapp = ?");
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['nombre'];
    }
    return false;
}
