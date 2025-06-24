<?php
// Dirección del técnico y servicios (ejemplo)
$origen = "Calle 1, Colonia Centro, CDMX";
$destino = "Calle 4, Colonia Del Valle, CDMX";
$waypoints = [
    "Calle 2, Colonia Roma, CDMX",
    "Calle 3, Colonia Condesa, CDMX"
];

// API Key de Google
$apiKey = "TU_API_KEY";

// Codificar direcciones
$origenEncoded = urlencode($origen);
$destinoEncoded = urlencode($destino);
$waypointsEncoded = urlencode(implode("|", $waypoints));

// Construir URL de Google Maps
$url = "https://www.google.com/maps/dir/?api=1";
$url .= "&origin={$origenEncoded}";
$url .= "&destination={$destinoEncoded}";
$url .= "&waypoints={$waypointsEncoded}";
$url .= "&travelmode=driving";

// Mostrar el link
echo "Ruta sugerida para el técnico:<br>";
echo "<a href='{$url}' target='_blank'>Ver Ruta en Google Maps</a>";
?>
