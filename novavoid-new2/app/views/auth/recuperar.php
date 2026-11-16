<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Nova Void Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 430px;">
        <h3 class="text-center fw-bold mb-2">Recuperar Contraseña</h3>
        <p class="text-muted text-center small mb-4">
            Ingresa tu correo electrónico registrado y define una nueva contraseña segura.
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= $ruta ?>auth/procesarRecuperar" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="tu@correo.com">
            </div>

            <div class="mb-3">
                <label for="nueva_password" class="form-label fw-bold">Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" id="nueva_password" name="nueva_password" class="form-control" required placeholder="Mínimo 8 caracteres">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('nueva_password')">
                        👁️
                    </button>
                </div>
                <div class="form-text small">Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 símbolo especial.</div>
            </div>

            <div class="mb-4">
                <label for="confirmar_password" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" required placeholder="Repite tu nueva contraseña">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('confirmar_password')">
                        👁️
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 fw-bold py-2">Actualizar Contraseña</button>

            <div class="text-center mt-3">
                <a href="<?= $ruta ?>auth/login" class="text-decoration-none text-muted small">Volver al inicio de sesión</a>
            </div>
        </form>
    </div>

    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>