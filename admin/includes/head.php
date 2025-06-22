<head>
  <meta charset="UTF-8">
  <title>Panel Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Estilos globales opcionales -->
  <style>
    body {
      margin: 0;
      background-color: #f9fafb; /* gris clarito */
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .top-bar .menu-toggle {
      display: none;
      font-size: 24px;
      background: none;
      border: none;
    }

    @media (max-width: 768px) {
      .top-bar .menu-toggle {
        display: block;
      }
    }
  </style>
</head>
