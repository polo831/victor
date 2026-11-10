<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $datos['titulo'] ?? 'Pedido Exitoso' ?> - Nova Void Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $head ?? '' ?>
</head>
<body class="bg-light">

    <?= $topbar ?? '' ?>

    <div class="container my-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-5 bg-white">
                    <div class="card-body">
                        <div class="mb-4 text-success display-4 fw-bold">✓</div>
                        <h2 class="fw-bold mb-3">¡Compra realizada con éxito!</h2>
                        <p class="text-muted mb-4">
                            Tu pedido ha sido registrado correctamente en nuestro sistema. 
                            Pronto nos pondremos en contacto contigo para coordinar el envío y los detalles de pago.
                        </p>
                        <a href="<?= $ruta ?? '' ?>home" class="btn btn-dark w-100 py-2 fw-bold text-uppercase" style="letter-spacing: 0.5px;">Volver al Catálogo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>