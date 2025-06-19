function tieneRol($rol) {
    return in_array($rol, $_SESSION['usuario_roles'] ?? []);
}
