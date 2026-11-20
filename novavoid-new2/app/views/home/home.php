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

    <!-- Sección de Medios de Contacto y Redes Sociales -->
    <footer id="contacto" class="py-5" style="background-color: #f7f5f2; border-top: 1px solid #e9e5df;">
        <div class="container text-center">
            <h3 class="mb-4 text-uppercase fw-bold" style="letter-spacing: 3px; color: #514e3d; font-size: 1.35rem;">Contacto & Redes</h3>
            <div class="row justify-content-center gy-4">
                
                <!-- Instagram -->
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 10px; background: #ffffff;">
                        <div class="mb-3" style="color: #514e3d;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold text-uppercase mb-2" style="font-size: 1.05rem;">Instagram</h5>
                        <p class="mb-0">
                            <a href="https://instagram.com/novavoid" target="_blank" class="text-decoration-none fw-semibold" style="color: #514e3d;">@novavoid</a>
                        </p>
                    </div>
                </div>

                <!-- Dirección -->
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 10px; background: #ffffff;">
                        <div class="mb-3" style="color: #514e3d;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold text-uppercase mb-2" style="font-size: 1.05rem;">Dirección</h5>
                        <p class="mb-0 text-muted fw-semibold">Necochea 2024</p>
                    </div>
                </div>

                <!-- Correo -->
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 10px; background: #ffffff;">
                        <div class="mb-3" style="color: #514e3d;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold text-uppercase mb-2" style="font-size: 1.05rem;">Correo</h5>
                        <p class="mb-0">
                            <a href="mailto:novavoid@gmail.com" class="text-decoration-none fw-semibold" style="color: #514e3d;">novavoid@gmail.com</a>
                        </p>
                    </div>
                </div>

            </div>

            <div class="mt-5 pt-4 border-top text-muted small">
                <p class="mb-0">&copy; <?= date('Y') ?> <strong>Nova Void Store</strong>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>