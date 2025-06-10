<?php
// your_project/tecnico/descargar_hs.php

// Incluye tu archivo de configuraci��n de base de datos
// La ruta asume que config.php est�� en la ra��z de tu proyecto
require_once __DIR__ . '/../config.php';

// Incluye la clase PdfGenerator
// La ruta asume que lib/PdfGenerator.php est�� en la ra��z de tu proyecto
require_once __DIR__ . '/../lib/PdfGenerator.php';

// Opcional: Autenticaci��n de sesi��n
session_start();
// Ajusta la l��gica de roles seg��n tus necesidades (por ejemplo, 'tecnico' y 'admin' pueden acceder)
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'tecnico' && $_SESSION['user_role'] != 'admin')) {
    header('Location: ../login.php'); // Redirige al login si no est�� autorizado
    exit();
}

// Establecer conexi��n a la base de datos
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    // Redirige al dashboard.php si hay un error de conexi��n
    header('Location: dashboard.php?status=error&message=' . urlencode('Error de conexi��n a la base de datos: ' . $conn->connect_error));
    exit();
}

// Obtener el ID del ticket de la URL
$ticket_id = isset($_GET['ticket_id']) ? trim($_GET['ticket_id']) : '';

// Instanciar la clase PdfGenerator y generar el PDF
$pdfGenerator = new PdfGenerator($conn);
$result = $pdfGenerator->generateServiceSheetPdf($ticket_id);

$conn->close(); // Cierra la conexi��n a la base de datos

if ($result['status'] == 'success') {
    // Env��a el PDF al navegador
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $result['filename'] . '"');
    echo $result['pdf_output'];
    exit();
} else {
    // Redirige al dashboard.php con un mensaje de error
    header('Location: dashboard.php?status=error&message=' . urlencode($result['message']));
    exit();
}
?>