<?php
require 'config.php';

// Datos del usuario
$login = "agcp@omnipos.com";
$password = "uno";

try {
    $stmt = $pdo->prepare("
        SELECT password 
        FROM usuarios 
        WHERE email = :login OR username = :login
    ");
    $stmt->execute([':login' => $login]);
    $result = $stmt->fetch();

    if ($result && password_verify($password, $result['password'])) {
        echo "🎉 ¡Autenticación exitosa!";
    } else {
        echo "❌ Falló la verificación";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>