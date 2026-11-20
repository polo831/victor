<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;
use app\models\ProductoModel;
use app\models\CategoriaModel;

class ProductosController extends Controller
{
    // Constructor
    public function __construct()
    {
        self::$sessionStatus = SessionController::sessionVerificacion();
    }

    /* Método por defecto para la ruta /productos */
    public function actionIndex()
    {
        return $this->actioncatalogo();
    }

    /* Vista limpia del catálogo completo y filtrado por categoría/búsqueda */
    public function actioncatalogo()
    {
        static::path();
        $ruta = self::$path;

        // Capturamos si viene una categoría o búsqueda por GET
        $categoriaId = $_GET['categoria'] ?? null;
        $buscar = trim($_GET['buscar'] ?? '');

        // Obtenemos todas las categorías para los botones de filtro
        $categorias = CategoriaModel::obtenerTodasCategorias();

        // Traemos todos los productos y filtramos si corresponde
        $todosLosProductos = ProductoModel::obtenerTodos();
        $productos = [];

        if (!empty($todosLosProductos)) {
            foreach ($todosLosProductos as $prod) {
                $catProd = $prod->id_categoria ?? $prod->categoria_id ?? null;
                $nombreProd = $prod->nombre_prenda ?? '';

                // Filtro por categoría
                if ($categoriaId && $catProd != $categoriaId) {
                    continue;
                }

                // Filtro por buscador
                if ($buscar !== '' && stripos($nombreProd, $buscar) === false) {
                    continue;
                }

                $productos[] = $prod;
            }
        }

        $nombre_de_archivoDeVista = 'catalogo';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "productos" => $productos,
            "categorias" => $categorias,
            "ruta" => $ruta,
            "js" => self::loadJS(["path" => self::$path], 'carrito'),
        ];

        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /* recibo el id del producto mediante el parámetro */
    public function actiondetalle($id_producto)
    {
        SessionController::onlyUsers();
        /* sirve para no usar tantos ../ */
        $ruta = self::$path;

        $producto = ProductoModel::obtenerPorId($id_producto);

        /*if (empty($producto)) {
            $ruta = self::$path.'home';
            header("Location: $ruta");
        }*/

        static::path();
        $nombre_de_archivoDeVista = 'detalle';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "producto" => $producto,
            "ruta" => $ruta,
            "js" => self::loadJS(["path" => self::$path], 'carrito'),
        ];
        
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }
}