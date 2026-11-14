<?php 
namespace app\controllers;
use \Controller;

class SiteController extends Controller
{
    // Constructor
    public function __construct(){
        self::$sessionStatus = SessionController::sessionVerificacion();
    }

	public static function head(){
		static::ruta();
		$camino = self::$ruta;
		$camino = str_replace('&', '', $camino);
		$head = file_get_contents(APP_PATH.'/views/inc/head.php');
		$head = str_replace('#PATH#', $camino, $head);
		return $head;
	}

	public static function menu() {
	    static::path();
	    $menu = file_get_contents(APP_PATH . '/views/inc/menu-usuario.php');

	    // Obtener el número del mes actual
	    $numero_mes = date('n'); // 1-12
	    $arr = array(
	        "#PATH#" => self::$path,
	        "#MES_NUMERO#" => $numero_mes, // Agregar el número del mes
	    );

	    $menu = strtr($menu, $arr);
	    return $menu;
	}


    public static function menu_res(){
		static::path();
	    $menu = file_get_contents(APP_PATH.'/views/inc/menu-usuario-res.php');
		
		$arr = array(
		    "#PATH#" 	=> self::$path,
		);
		$menu = strtr($menu,$arr);
		return $menu;
	}


	public static function header($from_request = null){
	}

	public static function topbar() {
	    static::path();
	    
	    $filePath = APP_PATH . '/views/inc/topbar.php';
	    if (!file_exists($filePath)) {
	        return '';
	    }
	    
	    $topbar = file_get_contents($filePath);
	    
	    // Obtener los datos de sesión de forma segura
	    $nombreUsuario = isset($_SESSION['USER']['name']) ? htmlspecialchars($_SESSION['USER']['name']) : 'Usuario';
	    $isLogged = isset($_SESSION['USER']['id_usuario']);
	    $rolUsuario = $_SESSION['USER']['rol'] ?? 'cliente';

	    // Cantidad total de productos en el carrito
	    $cartCount = 0;
	    if (!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
	        foreach ($_SESSION['carrito'] as $item) {
	            $cartCount += (int)($item['cantidad'] ?? 1);
	        }
	    }

	    // Submenú de categorías para el menú lateral (drawer)
	    $categoriasSubmenu = '';
	    try {
	        if (class_exists('app\models\CategoriaModel')) {
	            $categorias = \app\models\CategoriaModel::obtenerTodasCategorias();
	            if (!empty($categorias)) {
	                foreach ($categorias as $cat) {
	                    $idCat = $cat->id_categoria ?? $cat->id ?? '';
	                    $nomCat = htmlspecialchars($cat->nombre_categoria ?? $cat->nombre ?? '');
	                    $categoriasSubmenu .= '<a href="' . static::$path . 'productos?categoria=' . $idCat . '" class="drawer-sublink">' . $nomCat . '</a>';
	                }
	            }
	        }
	    } catch (\Throwable $e) {}

	    // Construir dinámicamente el fragmento HTML según la sesión
	    if ($isLogged) {
	        $authSection = '
	            <li class="nav-item">
	                <span class="navbar-text fw-semibold text-light me-2">Hola, ' . $nombreUsuario . '</span>
	            </li>
	            <li class="nav-item">
	                <a class="btn btn-outline-light btn-sm px-3" href="' . static::$path . 'perfil">Mi Perfil</a>
	            </li>
	            <li class="nav-item ms-2">
	                <a class="btn btn-outline-danger btn-sm px-3" href="' . static::$path . 'auth/logout">Cerrar Sesión</a>
	            </li>';

	        $drawerAuth = '
	            <div class="d-flex align-items-center gap-2 mb-2">
	                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
	                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
	                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
	                </svg>
	                <span class="fw-semibold text-dark">' . $nombreUsuario . '</span>
	            </div>
	            <div class="d-flex flex-wrap gap-2 small">
	                <a href="' . static::$path . 'perfil" class="drawer-auth-link">Mi Perfil</a>
	                ' . ($rolUsuario === 'admin' ? '<span class="text-muted">•</span><a href="' . static::$path . 'admin/panel" class="drawer-auth-link fw-bold text-primary">Panel Admin</a>' : '') . '
	                <span class="text-muted">•</span>
	                <a href="' . static::$path . 'auth/logout" class="drawer-auth-link text-danger">Cerrar Sesión</a>
	            </div>';
	    } else {
	        $authSection = '
	            <li class="nav-item">
	                <a class="btn btn-light btn-sm px-3 fw-bold" href="' . static::$path . 'auth/login">Iniciar Sesión</a>
	            </li>';

	        $drawerAuth = '
	            <div class="d-flex align-items-center gap-2">
	                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person text-secondary" viewBox="0 0 16 16">
	                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
	                </svg>
	                <a href="' . static::$path . 'auth/registro" class="drawer-auth-link">Crear cuenta</a>
	                <span class="text-muted">|</span>
	                <a href="' . static::$path . 'auth/login" class="drawer-auth-link">Iniciar sesión</a>
	            </div>';
	    }

	    // Array de reemplazo para strtr manteniendo tu estructura original
	    $arr = array(
	        "#PATH#"                => static::$path,
	        "#AUTH_SECTION#"        => $authSection,
	        "#AUTH_DRAWER_SECTION#" => $drawerAuth,
	        "#CATEGORIAS_SUBMENU#"  => $categoriasSubmenu,
	        "#CART_COUNT#"          => (string)$cartCount,
	        "#USER_NAME#"           => $nombreUsuario
	    );
	    
	    $topbar = strtr($topbar, $arr);
	    return $topbar;
	}

	public static function footer(){
		static::path();
		$var = '';
		$footer = file_get_contents(APP_PATH.'/views/inc/footer.php');
		$footer_registro = 'un dato';
		$arr = array(
		    "#var#"		=> $var,
		    "#FOOTER_DATO#" => $footer_registro,
		    "#PATH#" 			=> self::$path);
		$footer = strtr($footer,$arr);
		return $footer;
	}

}
