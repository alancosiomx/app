<?php
// descarga_pdf.php

if (empty($_GET['uuid'])) {
    die('Falta el UUID de la factura.');
}

$uuid = $_GET['uuid'];
$token = 'TU_FISCALPOP_TOKEN_AQUI'; // Cambia por tu token

$url = "https://api.fiscalpop.com/api/v1/cfdi/pdf/$uuid?token=$token";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$pdf = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    die("Error al descargar PDF. HTTP code: $http_code");
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="factura_'.$uuid.'.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
exit;
