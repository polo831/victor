<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto->nombre_prenda ?? 'Producto') ?> - Nova Void Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }
        .btn-negro {
            background-color: #212529;
            color: #fff;
        }
        .btn-negro:hover {
            background-color: #000;
            color: #fff;
        }
        .img-detalle {
            max-height: 450px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
    <?= $head ?>
</head>
<body>

    <!-- Navbar -->
    <?= $topbar ?>

    <!-- Contenido del Detalle del Producto -->
    <div class="container py-5">
        <?php if (!empty($producto)): ?>
            <div class="row align-items-center bg-white p-4 p-md-5 rounded shadow-sm">
                
                <!-- Columna de la Imagen -->
                <div class="col-md-6 mb-4 mb-md-0 text-center">
                    <img src="<?= $ruta ?>img/<?= htmlspecialchars($producto->imagen ?? 'default.jpg') ?>" 
                         class="img-fluid img-detalle w-100 shadow-sm" 
                         alt="<?= htmlspecialchars($producto->nombre_prenda) ?>">
                </div>

                <!-- Columna de la Información -->
                <div class="col-md-6 ps-md-5">
                    <span class="badge bg-secondary mb-2">Prenda Exclusiva</span>
                    <h1 class="fw-bold mb-3"><?= htmlspecialchars($producto->nombre_prenda) ?></h1>
                    <h3 class="text-dark fw-bold mb-4">$<?= number_format($producto->precio ?? 0, 2, ',', '.') ?></h3>
                    
                    <p class="text-muted mb-4"><?= htmlspecialchars($producto->descripcion ?? 'Sin descripción detallada para esta prenda.') ?></p>

                    <!-- Selección de Talle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Seleccionar Talle:</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-dark btn-sm">S</button>
                            <button type="button" class="btn btn-outline-dark btn-sm">M</button>
                            <button type="button" class="btn btn-outline-dark btn-sm">L</button>
                            <button type="button" class="btn btn-outline-dark btn-sm">XL</button>
                        </div>
                    </div>

                    <!-- Botón de acción para el carrito -->
                    <form action="<?= $ruta ?>carrito/agregar_producto_lista" method="POST">
                <input type="hidden" name="id_producto" value="<?= $producto->id_producto ?>">
                <button type="submit" class="btn btn-negro btn-lg w-100 py-3 shadow-sm">Agregar al Carrito</button>
                </form>

                  
                </div>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <h3>El producto solicitado no existe o no está disponible.</h3>
                <a href="<?= $ruta ?>home" class="btn btn-dark mt-3">Volver al Catálogo</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>