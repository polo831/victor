<!-- Estilos del Header y Menú Giusto Denim Style -->
<style>
    :root {
        --color-anuncio: #3d382c;
        --color-header: #514e3d;
        --color-header-hover: #434032;
    }

    /* Barra de promociones superior */
    .announcement-bar {
        background-color: var(--color-anuncio);
        color: #ffffff;
        font-size: 11.5px;
        letter-spacing: 0.5px;
        font-weight: 500;
        padding: 7px 15px;
        text-align: center;
    }

    /* Header principal */
    .store-header {
        background-color: var(--color-header);
        color: #ffffff;
        padding: 12px 20px;
        position: sticky;
        top: 0;
        z-index: 1030;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
    }

    .store-header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Sección Izquierda: Botón Hamburguesa y Buscador */
    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .btn-hamburger {
        background: transparent;
        border: none;
        color: #ffffff;
        padding: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    .btn-hamburger:hover {
        transform: scale(1.08);
        color: #f0f0f0;
    }

    .header-search {
        position: relative;
        width: 100%;
        max-width: 220px;
    }

    .header-search input {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 50px;
        color: #ffffff;
        padding: 5px 35px 5px 15px;
        font-size: 13px;
        width: 100%;
        transition: all 0.25s ease;
    }

    .header-search input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .header-search input:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: #ffffff;
        color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
    }

    .header-search-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #ffffff;
        padding: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    /* Sección Centro: Logo */
    .header-center {
        flex: 1;
        text-align: center;
    }

    .brand-logo {
        text-decoration: none;
        color: #ffffff;
        display: inline-block;
    }

    .brand-logo-title {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: 4px;
        line-height: 1;
        text-transform: uppercase;
        margin: 0;
    }

    .brand-logo-sub {
        font-size: 8.5px;
        letter-spacing: 2.5px;
        color: rgba(255, 255, 255, 0.75);
        text-transform: uppercase;
        margin-top: 3px;
        display: block;
    }

    /* Sección Derecha: Carrito */
    .header-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 15px;
        flex: 1;
    }

    .header-cart-btn {
        position: relative;
        color: #ffffff;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 4px;
        transition: transform 0.2s ease;
    }

    .header-cart-btn:hover {
        transform: scale(1.08);
        color: #ffffff;
    }

    .cart-badge-pill {
        position: absolute;
        top: -6px;
        right: -10px;
        background: #ffffff;
        color: #3d382c;
        font-size: 11px;
        font-weight: 800;
        border-radius: 50%;
        min-width: 19px;
        height: 19px;
        line-height: 19px;
        text-align: center;
        padding: 0 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    }

    /* Menú Lateral (Offcanvas Drawer) */
    .offcanvas-giusto {
        width: 320px !important;
        border: none;
        box-shadow: 4px 0 25px rgba(0, 0, 0, 0.25);
    }

    .offcanvas-giusto .btn-close-custom {
        background: transparent;
        border: none;
        font-size: 22px;
        line-height: 1;
        color: #333333;
        cursor: pointer;
        padding: 5px;
    }

    .drawer-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .drawer-menu-item {
        border-bottom: 1px solid #f0f0f0;
    }

    .drawer-link {
        display: block;
        padding: 13px 4px;
        color: #1a1a1a;
        font-size: 15.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .drawer-link:hover {
        color: var(--color-header);
        padding-left: 6px;
    }

    .drawer-chevron {
        font-size: 14px;
        color: #777777;
        font-weight: 400;
        transition: transform 0.25s ease;
    }

    .drawer-submenu {
        padding: 8px 0 12px 14px;
        border-left: 2px solid #eaeaea;
        margin-left: 8px;
        margin-bottom: 8px;
    }

    .drawer-sublink {
        display: block;
        padding: 5px 0;
        color: #555555;
        font-size: 14px;
        text-decoration: none;
        transition: color 0.2s;
    }

    .drawer-sublink:hover {
        color: #000000;
        font-weight: 500;
    }

    .drawer-auth-bottom {
        padding-top: 18px;
        margin-top: auto;
        border-top: 1px solid #e5e5e5;
    }

    .drawer-auth-link {
        color: #1a1a1a;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .drawer-auth-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .header-search {
            display: none;
        }
        .brand-logo-title {
            font-size: 1.5rem;
        }
        .announcement-bar {
            font-size: 10.5px;
            padding: 5px 8px;
        }
    }
</style>

<!-- Barra de Anuncios Superior -->
<div class="announcement-bar">
    <span>15% OFF con TRANSFERENCIA &nbsp;|&nbsp; Envío Gratis y Hasta 3 Cuotas sin Interés en compras mayores a $120.000</span>
</div>

<!-- Header Principal -->
<header class="store-header">
    <div class="store-header-container">
        
        <!-- Izquierda: Menú Hamburguesa + Buscador -->
        <div class="header-left">
            <button class="btn-hamburger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasStoreMenu" aria-controls="offcanvasStoreMenu" aria-label="Abrir menú">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </button>

            <form action="#PATH#productos" method="GET" class="header-search">
                <input type="text" name="buscar" placeholder="Buscar">
                <button type="submit" class="header-search-btn" aria-label="Buscar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Centro: Logo de la Tienda -->
        <div class="header-center">
            <a href="#PATH#home" class="brand-logo">
                <div class="brand-logo-title">NOVA VOID</div>
                <div class="brand-logo-sub">IT'S ALL IN DETAILS</div>
            </a>
        </div>

        <!-- Derecha: Carrito de compras -->
        <div class="header-right">
            <a href="#PATH#carrito" class="header-cart-btn" aria-label="Ver carrito">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span class="cart-badge-pill">#CART_COUNT#</span>
            </a>
        </div>

    </div>
</header>

<!-- Menú Lateral Desplegable (Offcanvas Drawer) -->
<div class="offcanvas offcanvas-start offcanvas-giusto" tabindex="-1" id="offcanvasStoreMenu" aria-labelledby="offcanvasStoreMenuLabel">
    <div class="offcanvas-header justify-content-end p-3">
        <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Cerrar">✕</button>
    </div>

    <div class="offcanvas-body d-flex flex-column justify-content-between px-4 pb-4 pt-0">
        <ul class="drawer-menu-list">
            <li class="drawer-menu-item">
                <a class="drawer-link" href="#PATH#home">Inicio</a>
            </li>
            <li class="drawer-menu-item">
                <div class="d-flex justify-content-between align-items-center">
                    <a class="drawer-link flex-grow-1" href="#PATH#productos">Productos</a>
                    <button class="btn btn-link text-dark p-2 border-0 shadow-none text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProductosDrawer" aria-expanded="false" aria-controls="collapseProductosDrawer" aria-label="Ver categorías">
                        <span class="drawer-chevron">↓</span>
                    </button>
                </div>
                <div class="collapse" id="collapseProductosDrawer">
                    <div class="drawer-submenu">
                        <a href="#PATH#productos" class="drawer-sublink fw-bold">Ver todos los productos</a>
                        #CATEGORIAS_SUBMENU#
                    </div>
                </div>
            </li>
            <li class="drawer-menu-item">
                <a class="drawer-link" href="#PATH#home#cambios">Cambios & Devoluciones</a>
            </li>
            <li class="drawer-menu-item">
                <a class="drawer-link" href="#PATH#home#preguntas">Preguntas Frecuentes</a>
            </li>
            <li class="drawer-menu-item">
                <a class="drawer-link" href="#PATH#home#mayorista">Venta Mayorista</a>
            </li>
            <li class="drawer-menu-item">
                <a class="drawer-link" href="#PATH#home#contacto">Contacto</a>
            </li>
        </ul>

        <!-- Sección de Usuario / Cuenta en la parte inferior del menú -->
        <div class="drawer-auth-bottom">
            #AUTH_DRAWER_SECTION#
        </div>
    </div>
</div>
