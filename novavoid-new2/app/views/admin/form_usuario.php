<!DOCTYPE html>
<html lang="es">
<head>
    <?= $parametros_de_vista['head'] ?? $head ?? '' ?>
    <title><?= $datos['titulo'] ?? 'Formulario Usuario' ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #343a40; color: white; padding: 20px; }
        .sidebar h2 { font-size: 20px; text-align: center; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 10px 0; border-bottom: 1px solid #4f5962; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; }
        .sidebar ul li a:hover { color: #17a2b8; }
        .main-content { flex: 1; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; border: none; cursor: pointer; display: inline-block; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
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
        <main class="main-content">
            <div class="card">
                <h1><?= $datos['titulo'] ?? 'Usuario' ?></h1>
                <hr>
                

                <form action="<?= $ruta ?>admin/guardarUsuario" method="POST" style="margin-top: 20px;">
                    <input type="hidden" name="id" value="<?= $datos['usuario']->id ?? '' ?>">

                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($datos['usuario']->nombre ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($datos['usuario']->email ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña <?= isset($datos['usuario']) ? '(Dejar en blanco para mantener la actual)' : '' ?>:</label>
                        <input type="password" id="password" name="password" <?= isset($datos['usuario']) ? '' : 'required' ?>>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($datos['usuario']->direccion ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($datos['usuario']->telefono ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="rol">Rol:</label>
                        <select id="rol" name="rol" required>
                            <option value="cliente" <?= (isset($datos['usuario']->rol) && $datos['usuario']->rol == 'cliente') ? 'selected' : '' ?>>Cliente</option>
                            <option value="admin" <?= (isset($datos['usuario']->rol) && $datos['usuario']->rol == 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
                        <a href="<?= $ruta ?>admin/usuarios" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>