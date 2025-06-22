<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script> <style>
  /* --- REGLAS CSS QUE SÍ DEBES MANTENER (si las usas) --- */

  body {
    margin: 0; /* Asegura que no haya márgenes por defecto del navegador */
    background-color: #f9fafb; /* Color de fondo general */
    /* Fuente: usa la que prefieras. Tailwind tiene sus propias clases de fuentes. */
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  /*
  Reglas para la barra superior (top-bar):
  Si estás usando clases de Tailwind como 'bg-white shadow sticky top-0 z-30'
  en tu <header> principal, entonces estas reglas de .top-bar pueden ser redundantes
  o causar conflictos.

  Si quieres que tu barra superior sea fija y no la manejas totalmente con Tailwind,
  podrías dejar esta parte, pero asegurándote de que no pelee con las clases
  de 'sticky' o 'fixed' de Tailwind en el header de tu layout.php.
  */
  .top-bar {
    /* position: fixed; */ /* COMENTA si tu <header> en layout.php ya usa 'sticky' o 'fixed' de Tailwind */
    top: 0;
    left: 0;
    right: 0;
    background-color: #f1f5f9;
    color: #0f172a;
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
    z-index: 1050;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  /* Botón de alternar menú móvil (menu-toggle): */
  /* Si el botón de tu header tiene la clase 'md:hidden', ya se oculta en escritorio. */
  /* Esto controla cómo se ve el icono cuando está visible. */
  .top-bar .menu-toggle {
    display: none; /* Por defecto oculto, Tailwind lo muestra con 'md:hidden' */
    font-size: 24px;
    background: none;
    border: none;
  }

  @media (max-width: 768px) {
    .top-bar .menu-toggle {
      display: block; /* Solo muestra el botón en pantallas pequeñas */
    }
  }

  /* --- REGLAS CSS QUE CASI SEGURO DEBES ELIMINAR O COMENTAR --- */
  /* Estas reglas son las que causaban el conflicto con el layout de Tailwind */
  /* que hemos puesto en layout.php */

  /* .sidebar { ... }  <-- ¡ELIMINA O COMENTA ESTO! */
  /* .sidebar.collapsed { ... } <-- ¡ELIMINA O COMENTA ESTO! */
  /* .sidebar a { ... } <-- Puedes mantenerlo si son estilos de enlaces MUY específicos que Tailwind no cubre para tu menú */
  /* .sidebar a:hover { ... } <-- Igual que lo anterior */

  /* @media (max-width: 768px) { .sidebar { ... } } <-- ¡ELIMINA O COMENTA ESTO! */

  /* .main-content { ... } <-- ¡ELIMINA O COMENTA ESTO! */
  /* @media (max-width: 768px) { .main-content { ... } } <-- ¡ELIMINA O COMENTA ESTO! */
  /* .main-content.collapsed { ... } <-- ¡ELIMINA O COMENTA ESTO! */

</style>
