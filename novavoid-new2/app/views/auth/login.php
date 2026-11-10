<?php
// Vista de Login de Cliente / Usuario Común
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Noda Void</title>
    <!-- Bootstrap 5 CSS CDN para que tome todos los estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?=$head ?? ''?>
</head>
<body class="bg-light d-flex align-items-center py-5" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 p-4">
                    <div class="card-body">
                        <h2 class="mb-4 text-center fw-bold">Iniciar Sesión</h2>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST">
                            <!-- Campo de Correo -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="tu@correo.com">
                            </div>
                            
                            <!-- Campo de Contraseña con el botón Ver -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        👁️
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 py-2">Ingresar</button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">¿No tienes una cuenta? <a href="<?=$ruta ?? ''?>auth/register" class="text-decoration-none fw-semibold text-dark">Regístrate aquí</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script limpio para mostrar/ocultar contraseña -->
    <script>
        function togglePassword(idInput) {
            const input = document.getElementById(idInput);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        }
    </script>
</body>
</html>