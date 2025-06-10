<?php
$clave = '123456';
echo 'Hash generado para "' . $clave . '":<br>';
echo password_hash($clave, PASSWORD_DEFAULT);
?>
