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
        .btn-talle.active,
        .btn-color.active {
            background-color: #212529;
            color: #fff;
            border-color: #212529;
        }
    </style>
    <?= $head ?>
    <?= $js ?>
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

                    <!-- Selección de Talle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Seleccionar Talle:</label>
                        <div class="d-flex gap-2" id="grupoTalles">
                            <button type="button" class="btn btn-outline-dark btn-sm btn-talle" data-talle="S">S</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-talle" data-talle="M">M</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-talle" data-talle="L">L</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-talle" data-talle="XL">XL</button>
                        </div>
                        <small id="avisoTalle" class="text-danger d-none mt-1">Por favor, seleccioná un talle antes de agregar al carrito.</small>
                    </div>

                    <!-- Selección de Color -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Seleccionar Color:</label>
                        <div class="d-flex gap-2 flex-wrap" id="grupoColores">
                            <button type="button" class="btn btn-outline-dark btn-sm btn-color" data-color="Negro">Negro</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-color" data-color="Blanco">Blanco</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-color" data-color="Marrón">Marrón</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-color" data-color="Gris">Gris</button>
                        </div>
                        <small id="avisoColor" class="text-danger d-none mt-1">Por favor, seleccioná un color antes de agregar al carrito.</small>
                    </div>

                    <input type="hidden" name="id_producto" id="id_producto" value="<?= $producto->id_producto ?>">
                    <input type="hidden" name="talle" id="talleSeleccionado" value="">
                    <input type="hidden" name="color" id="colorSeleccionado" value="">
                    <button type="button"
                            id="agregar"
                            data-producto="<?= $producto->id_producto ?>"
                            data-talle=""
                            data-color=""
                            class="btn btn-negro btn-lg w-100 py-3 shadow-sm">
                        Agregar al Carrito
                    </button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const botonesTalle = document.querySelectorAll('.btn-talle');
            const botonesColor = document.querySelectorAll('.btn-color');
            const inputTalle = document.getElementById('talleSeleccionado');
            const inputColor = document.getElementById('colorSeleccionado');
            const btnAgregar = document.getElementById('agregar');
            const avisoTalle = document.getElementById('avisoTalle');
            const avisoColor = document.getElementById('avisoColor');

            botonesTalle.forEach(function (boton) {
                boton.addEventListener('click', function () {
                    botonesTalle.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const talleElegido = this.getAttribute('data-talle');
                    if (inputTalle) inputTalle.value = talleElegido;
                    if (btnAgregar) btnAgregar.setAttribute('data-talle', talleElegido);
                    if (avisoTalle) avisoTalle.classList.add('d-none');
                });
            });

            botonesColor.forEach(function (boton) {
                boton.addEventListener('click', function () {
                    botonesColor.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const colorElegido = this.getAttribute('data-color');
                    if (inputColor) inputColor.value = colorElegido;
                    if (btnAgregar) btnAgregar.setAttribute('data-color', colorElegido);
                    if (avisoColor) avisoColor.classList.add('d-none');
                });
            });

            if (btnAgregar) {
                btnAgregar.addEventListener('click', function (e) {
                    const talleActual = inputTalle ? inputTalle.value : '';
                    const colorActual = inputColor ? inputColor.value : '';

                    if (!talleActual) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        if (avisoTalle) avisoTalle.classList.remove('d-none');
                        return;
                    }

                    if (!colorActual) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        if (avisoColor) avisoColor.classList.remove('d-none');
                        return;
                    }
                }, true);
            }
        });
    </script>
</body>
</html>