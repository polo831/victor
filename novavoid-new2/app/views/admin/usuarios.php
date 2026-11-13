<!DOCTYPE html>
<html lang="es">
<head>
    <?= $parametros_de_vista['head'] ?? $head ?? '' ?>
    <title><?= $datos['titulo'] ?? 'Gestión de Usuarios' ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #343a40; color: white; padding: 20px; }
        .sidebar h2 { font-size: 20px; text-align: center; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 10px 0; border-bottom: 1px solid #4f5962; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; }
        .sidebar ul li a:hover { color: #17a2b8; }
        .main-content { flex: 1; padding: 30px; overflow-x: auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        thead { background-color: #343a40; color: white; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px; display: inline-block; color: white; }
        .btn-primary { background: #007bff; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Menú lateral -->
        <nav class="sidebar">
            <h2>Panel Tienda</h2>
            <ul>
                <li><a href="<?= $ruta ?>admin/panel">Inicio</a></li>
                <li><a href="<?= $ruta ?>admin/productos">Gestionar Productos (ABM)</a></li>
                <li><a href="<?= $ruta ?>admin/usuarios">Gestionar Usuarios</a></li>
                <li><a href="<?= $ruta ?>admin/pedidos">Gestionar Pedidos</a></li>
                <li><a href="<?= $ruta ?>auth/logout">Cerrar Sesión</a></li>
            </ul>
        </nav>

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h1><?= htmlspecialchars($datos['titulo'] ?? 'Gestión de Usuarios') ?></h1>
                    <a href="<?= $ruta ?>admin/crearUsuario" class="btn btn-success">+ Nuevo Usuario</a>
                </div>
                <hr>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($datos['usuarios'])): ?>
                            <?php foreach ($datos['usuarios'] as $usu): ?>
                                <tr>
                                    <td><?= $usu->id ?></td>
                                    <td><?= htmlspecialchars($usu->nombre) ?></td>
                                    <td><?= htmlspecialchars($usu->email) ?></td>
                                    <td><?= htmlspecialchars($usu->direccion ?? '-') ?></td>
                                    <td><?= htmlspecialchars($usu->telefono ?? '-') ?></td>
                                    <td><strong><?= htmlspecialchars($usu->rol) ?></strong></td>
                                    <td>
                                        <a href="<?= $ruta ?>admin/editarUsuario&id=<?= $usu->id ?>" class="btn btn-primary">Editar</a>
                                        <a href="<?= $ruta ?>admin/eliminarUsuario&id=<?= $usu->id ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center;">No hay usuarios registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>