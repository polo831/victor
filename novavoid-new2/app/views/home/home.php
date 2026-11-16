<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Void Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?=$head?>
    <?=$js?>
    <style>
        body {
            background-color: #fbfbfb;
            color: #212529;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
        }

        /* Hero Banner ocupando la pantalla principal */
        .hero-banner-container {
            width: 100%;
            position: relative;
            background-color: #d8c3ad;
            overflow: hidden;
        }

        .hero-banner-img {
            width: 100%;
            height: calc(100vh - 95px); /* Ajusta al alto de la pantalla restando el header */
            min-height: 550px;
            display: block;
            object-fit: cover;
            object-position: center;
        }

        .hero-overlay-btn {
            position: absolute;
            bottom: 12%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffffff;
            color: #1a1a1a;
            padding: 12px 32px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: all 0.25s ease;
        }

        .hero-overlay-btn:hover {
            background-color: #514e3d;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Header y Menú Lateral -->
    <?=$topbar?>

    <!-- Solo Portada Principal -->
    <section class="hero-banner-container">
        <img src="<?=$ruta?>img/banner-home.webp" 
             alt="Nueva Colección Primavera - Verano 2027" 
             class="hero-banner-img">
        
        <!-- Botón directo al catálogo por si el usuario hace clic sobre la portada -->
        <a href="<?=$ruta?>productos/catalogo" class="hero-overlay-btn">Explorar Colección</a>
    </section>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>