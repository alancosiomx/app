<?php
$password = "uno";
$hash = '$2y$10$u8MFrZFmsSSGQKe97wvqxufzq95e6lAoAdPOCE.ZIm0fZJyrkVS8y';

echo password_verify($password, $hash) ? "✅ ÉXITO" : "❌ FALLO";
?>