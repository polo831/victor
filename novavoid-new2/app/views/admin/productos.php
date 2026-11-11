<!DOCTYPE html>
<html lang="es">
<head>
    <?= $parametros_de_vista['head'] ?? $head ?? '' ?>
    <title><?= htmlspecialchars($datos['titulo'] ?? 'Gestión de Productos') ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px 0;
            border-bottom: 1px solid #4f5962;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .sidebar ul li a:hover {
            color: #17a2b8;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-x: auto;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .tabla-productos th,
        .tabla-productos td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .tabla-productos thead {
            background: #343a40;
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-primary {
            background: #007bff;
        }

        .btn-danger {
            background: #dc3545;
        }
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
            <li>
                <a href="<?= $ruta ?>auth/logout">Cerrar Sesión</a>
            </li>
        </ul>
    </nav>

    <!-- Contenido principal -->
    <main class="main-content">

        <div class="card">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">

                <h1><?= htmlspecialchars($datos['titulo'] ?? 'Gestión de Productos') ?></h1>

                <a href="<?= $ruta ?>admin/crearProducto" class="btn btn-success">
                    + Agregar Producto
                </a>

            </div>

            <hr>

            <!-- Tabla de productos -->
            <table class="tabla-productos">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($datos['productos'])): ?>

                        <?php foreach ($datos['productos'] as $prod): ?>

                            <tr>

                                <td>
                                    <?= (int)$prod->id_producto ?>
                                </td>

                                <td style="font-weight: bold;">
                                    <?= htmlspecialchars($prod->nombre_prenda ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($prod->categoria_nombre ?? 'Sin categoría') ?>
                                </td>

                                <td>
                                    $<?= number_format((float)$prod->precio, 2, ',', '.') ?>
                                </td>

                                <td>
                                    <?= (int)($prod->stock ?? 0) ?>
                                </td>

                                <td style="text-align: center;">

                                    <a
                                        href="<?= $ruta ?>admin/editarProducto&id=<?= (int)$prod->id_producto ?>"
                                        class="btn btn-primary"
                                    >
                                        Editar
                                    </a>

                                    <a
                                        href="<?= $ruta ?>admin/eliminarProducto&id=<?= (int)$prod->id_producto ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este producto?');"
                                    >
                                        Eliminar
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #6c757d;">
                                No hay productos cargados en la base de datos.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </main>

</div>

</body>
</html>
