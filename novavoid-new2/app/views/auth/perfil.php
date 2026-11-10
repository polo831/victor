<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($datos['titulo'] ?? 'Mi Perfil') ?> - Nova Void Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $head ?? '' ?>
</head>
<body class="bg-light">

    <!-- Topbar dinámico del SiteController -->
    <?= $topbar ?? '' ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-4">
                    <div class="card-body">
                        <h2 class="mb-4 text-center fw-bold text-uppercase" style="letter-spacing: 1px;">Mi Perfil</h2>

                        <!-- Alerta de éxito -->
                        <?php if (!empty($datos['exito'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($datos['exito']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Alerta de error -->
                        <?php if (!empty($datos['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($datos['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de actualización -->
                        <form action="<?= $ruta ?? '' ?>perfil/actualizar" method="POST">
                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars(is_object($datos['usuario'] ?? null) ? ($datos['usuario']->nombre ?? '') : ($datos['usuario']['nombre'] ?? '')) ?>" required>
                            </div>

                            <!-- Correo (Solo lectura por seguridad) -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control bg-light" id="email" value="<?= htmlspecialchars(is_object($datos['usuario'] ?? null) ? ($datos['usuario']->email ?? '') : ($datos['usuario']['email'] ?? '')) ?>" disabled>
                                <div class="form-text">El correo electrónico no se puede modificar.</div>
                            </div>

                            <!-- Dirección -->
                            <div class="mb-3">
                                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars(is_object($datos['usuario'] ?? null) ? ($datos['usuario']->direccion ?? '') : ($datos['usuario']['direccion'] ?? '')) ?>" required>
                            </div>

                            <!-- Teléfono -->
                            <div class="mb-3">
                                <label for="telefono" class="form-label fw-semibold">Teléfono / Celular</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars(is_object($datos['usuario'] ?? null) ? ($datos['usuario']->telefono ?? '') : ($datos['usuario']['telefono'] ?? '')) ?>" required>
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-dark py-2">Guardar Cambios</button>
                                <a href="<?= $ruta ?? '' ?>home" class="btn btn-outline-secondary py-2">Volver al Inicio</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>