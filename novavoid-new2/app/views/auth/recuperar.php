<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Nova Void Store</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">

    <div class="card shadow border-0 rounded-4 p-4 bg-white" style="width: 100%; max-width: 440px;">
        <div class="text-center mb-3">
            <h2 class="fw-bold text-dark mb-1">Nova Void</h2>
            <h5 class="text-secondary fw-normal">Recuperar Contraseña</h5>
        </div>

        <!-- Indicador de pasos visual -->
        <div class="d-flex justify-content-center align-items-center mb-4 gap-2">
            <span class="badge rounded-pill <?= ($paso === 1) ? 'bg-dark text-white' : 'bg-secondary-subtle text-secondary' ?> px-3 py-2">
                1. Solicitar Código
            </span>
            <i class="bi bi-chevron-right text-muted small"></i>
            <span class="badge rounded-pill <?= ($paso === 2) ? 'bg-dark text-white' : 'bg-secondary-subtle text-secondary' ?> px-3 py-2">
                2. Cambiar Clave
            </span>
        </div>

        <?php if (!empty($mensajeExito)): ?>
            <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>
                <?= htmlspecialchars($mensajeExito) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($paso === 1): ?>
            <!-- PASO 1: Ingreso de correo electrónico -->
            <p class="text-muted text-center small mb-4">
                Ingresa tu correo electrónico registrado y te enviaremos un código de seguridad de 6 dígitos.
            </p>

            <form action="<?= $ruta ?>auth/procesarRecuperar" method="POST">
                <input type="hidden" name="accion" value="solicitar_codigo">

                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="email" name="email" class="form-control border-start-0 ps-0" required placeholder="tu@correo.com" autofocus>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-dark py-2 fw-semibold rounded-3 shadow-sm">
                        <i class="bi bi-send me-1"></i> Enviar Código de Verificación
                    </button>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= $ruta ?>auth/login" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                    </a>
                </div>
            </form>

        <?php else: ?>
            <!-- PASO 2: Ingreso de código de 6 dígitos y nueva contraseña -->
            <div class="alert alert-info py-2 small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Código enviado a: <strong><?= htmlspecialchars($emailRecuperar) ?></strong>
            </div>

            <form action="<?= $ruta ?>auth/procesarRecuperar" method="POST">
                <input type="hidden" name="accion" value="confirmar_codigo">

                <div class="mb-3">
                    <label for="codigo" class="form-label fw-semibold">Código de Verificación (6 dígitos)</label>
                    <input type="text" id="codigo" name="codigo" class="form-control form-control-lg text-center fw-bold" required maxlength="6" pattern="\d{6}" placeholder="123456" style="letter-spacing: 0.35em; font-family: monospace;" autofocus>
                    <div class="form-text small text-center">Revisa tu bandeja de entrada o spam. Expira en 15 minutos.</div>
                </div>

                <div class="mb-3">
                    <label for="nueva_password" class="form-label fw-semibold">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="nueva_password" name="nueva_password" class="form-control border-start-0 border-end-0 ps-0" required placeholder="Mínimo 8 caracteres">
                        <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePass('nueva_password', 'eyeIcon1')">
                            <i class="bi bi-eye" id="eyeIcon1"></i>
                        </button>
                    </div>
                    <div class="form-text small">Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 símbolo especial.</div>
                </div>

                <div class="mb-4">
                    <label for="confirmar_password" class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" id="confirmar_password" name="confirmar_password" class="form-control border-start-0 border-end-0 ps-0" required placeholder="Repite tu contraseña">
                        <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePass('confirmar_password', 'eyeIcon2')">
                            <i class="bi bi-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-dark py-2 fw-semibold rounded-3 shadow-sm">
                        <i class="bi bi-shield-check me-1"></i> Confirmar y Cambiar Contraseña
                    </button>
                </div>

                <div class="text-center mt-3 d-flex flex-column gap-2">
                    <a href="<?= $ruta ?>auth/procesarRecuperar?reiniciar=1" class="text-decoration-none text-muted small">
                        ¿No recibiste el código o te equivocaste de correo? <strong>Solicitar nuevo código</strong>
                    </a>
                    <a href="<?= $ruta ?>auth/login" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
