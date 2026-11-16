<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Nova Void Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?=$head?>
    <?=$js?>
    <style>
        body { background-color: #fbfbfb; color: #212529; }
        .category-filter-btn { border-radius: 30px; font-size: 13px; font-weight: 500; padding: 6px 20px; text-transform: uppercase; transition: all 0.2s ease; }
        .product-card { transition: transform 0.25s ease, box-shadow 0.25s ease; border-radius: 8px; overflow: hidden; background: #ffffff; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important; }
        .product-card-img { height: 320px; object-fit: cover; transition: transform 0.35s ease; }
        .product-card:hover .product-card-img { transform: scale(1.02); }
        .btn-comprar { background-color: #514e3d; color: #ffffff; border: none; font-weight: 600; border-radius: 6px; padding: 9px 0; transition: background-color 0.2s ease; }
        .btn-comprar:hover { background-color: #3d382c; color: #ffffff; }
    </style>
</head>
<body>

    <?=$topbar?>

    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-uppercase" style="letter-spacing: 2px; font-size: 2rem;">Catálogo Exclusivo</h1>
            <p class="text-muted small text-uppercase" style="letter-spacing: 1.5px;">Explorá nuestra colección de prendas</p>
        </div>

        <!-- Botones de Filtrado por Categorías -->
        <div class="d-flex justify-content-center gap-2 flex-wrap mb-5">
            <?php $todasActiva = empty($_GET['categoria']) ? 'btn-dark text-white' : 'btn-outline-dark'; ?>
            <a href="<?=$ruta?>productos/catalogo" class="btn <?=$todasActiva?> category-filter-btn">Todas</a>
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <?php 
                        $idCat = $categoria->id_categoria ?? $categoria->id;
                        $esActiva = (isset($_GET['categoria']) && $_GET['categoria'] == $idCat) ? 'btn-dark text-white' : 'btn-outline-dark';
                    ?>
                    <a href="<?=$ruta?>productos/catalogo?categoria=<?=$idCat?>" class="btn <?=$esActiva?> category-filter-btn">
                        <?=htmlspecialchars($categoria->nombre_categoria ?? $categoria->nombre)?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Grilla de Productos -->
        <div class="row">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 product-card">
                            <div class="overflow-hidden">
                                <img src="<?=$ruta?>img/<?=htmlspecialchars($producto->imagen ?? '')?>" class="card-img-top product-card-img" alt="<?=htmlspecialchars($producto->nombre_prenda ?? 'Producto')?>">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title fw-bold mb-2">
                                    <a href="<?=$ruta?>productos/detalle/<?= $producto->id_producto ?>" class="text-dark text-decoration-none">
                                        <?=htmlspecialchars($producto->nombre_prenda ?? 'Sin nombre')?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1"><?=htmlspecialchars($producto->descripcion ?? '')?></p>
                                <div class="mt-3 pt-3 border-top">
                                    <p class="fs-5 fw-bold text-dark mb-3">$<?=number_format($producto->precio ?? 0, 2, ',', '.')?></p>
                                    <a href="#" data-producto="<?= $producto->id_producto ?>" id="agregar" class="btn btn-comprar w-100">Comprar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">No se encontraron productos disponibles.</p>
                    <a href="<?=$ruta?>productos/catalogo" class="btn btn-dark category-filter-btn mt-3">Ver catálogo completo</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>