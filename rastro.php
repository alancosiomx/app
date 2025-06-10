<?php
echo "📍 RUTA REAL: " . getcwd();
echo "<br><br>🧾 Contenido actual de index.php:<br><pre>";
echo htmlspecialchars(file_get_contents("index.php"));
?>
