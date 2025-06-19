<div class="main-content">
  <h2>👨‍🔧 Gestión de Técnicos / Usuarios</h2>

  <div class="card mt-4">
    <div class="card-header">Crear nuevo usuario</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="accion" value="crear">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div class="row mb-2">
          <div class="col">
            <input name="nombre" required class="form-control" placeholder="Nombre completo">
          </div>
          <div class="col">
            <input name="email" required type="email" class="form-control" placeholder="Correo electrónico">
          </div>
          <div class="col">
            <input name="username" required class="form-control" placeholder="Usuario">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <label class="form-label">Roles</label>
            <?php foreach ($roles as $rol): ?>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $rol ?>">
                <label class="form-check-label"><?= ucfirst($rol) ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <input name="password" class="form-control" placeholder="Contraseña (opcional, por defecto 'uno')">
          </div>
          <div class="col text-end">
            <button class="btn btn-success">Crear Usuario</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <hr>

  <table class="table table-bordered table-striped mt-4 bg-white">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Usuario</th>
        <th>Activo</th>
        <th>Roles</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['nombre']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= $u['activo'] ? '✅' : '❌' ?></td>
          <td><?= implode(', ', $roles_usuario[$u['id']] ?? []) ?></td>
          <td>
            <a href="?reset=<?= $u['id'] ?>" class="btn btn-sm btn-warning" title="Reset password">🔑</a>
            <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">🗑️</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
