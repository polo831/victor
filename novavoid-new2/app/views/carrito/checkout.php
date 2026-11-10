<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $datos['titulo'] ?? 'Finalizar Compra' ?> - Nova Void Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $head ?? '' ?>
</head>
<body class="bg-light">

    <?= $topbar ?? '' ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-4 bg-white">
                    <h2 class="mb-4 fw-bold text-uppercase" style="letter-spacing: 1px;">Finalizar Pedido</h2>

                   <form action="" method="POST">
                 <!-- Tus campos de dirección y medio de pago -->
                 <div class="mb-3">
                 <label for="direccion" class="form-label fw-semibold">Dirección de Envío</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required placeholder="Calle, Número, Ciudad">
                </div>

                <div class="mb-3">
                <label for="medio_pago_id" class="form-label fw-semibold">Forma de Pago</label>
                <select name="medio_pago_id" id="medio_pago_id" class="form-select" required>
                <option value="">Selecciona un medio de pago</option>
                <option value="1">Efectivo (Contra entrega)</option>
                <option value="2">Transferencia Bancaria</option>
            </select>
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-dark py-2 fw-bold">Confirmar y Guardar Pedido</button>
        <a href="<?= $ruta ?? '' ?>carrito/index" class="btn btn-outline-secondary py-2">Volver al Carrito</a>
    </div>
</form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>