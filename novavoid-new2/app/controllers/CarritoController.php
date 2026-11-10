<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;
use app\models\UserModel;
use app\models\ProductoModel;
use app\models\CategoriaModel;

class CarritoController extends Controller
{
    // Constructor
    public function __construct()
    {
        self::$sessionStatus = SessionController::sessionVerificacion();
    }

    public function actionIndex($var = null)
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        static::path();
        $nombre_de_archivoDeVista = 'carrito';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "ruta" => $ruta,
            "total" => $total,
            "js" => self::loadJS(["path" => self::$path], 'carrito'),
        ];
        
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /* Agregar producto al carrito mediante jQuery (Adaptado a tus columnas nuevas) */
    public function actionagregar_producto_lista($var = null)
    {    
        $idProducto_para_agregar = $_POST['id_producto'] ?? null;
        $cantidad_enviada = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

        $producto = ProductoModel::obtenerPorId($idProducto_para_agregar);

        if (!$producto) {
            echo json_encode(['status' => false, 'mensaje' => 'Producto no encontrado']);
            return;
        }

        // Compatibilidad robusta: lee tanto si viene como objeto (->nombre_prenda) como array (['nombre_prenda'])
        $nombre = $producto[0]->nombre_prenda ?? $producto['nombre_prenda'] ?? $producto[0]->nombre ?? $producto['nombre'] ?? 'Producto';
        $precio = $producto[0]->precio ?? $producto['precio'] ?? 0;

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        if (isset($_SESSION['carrito'][$idProducto_para_agregar])) {
            $_SESSION['carrito'][$idProducto_para_agregar]['cantidad'] += $cantidad_enviada;
        } else {
            $_SESSION['carrito'][$idProducto_para_agregar] = [
                'id'       => $idProducto_para_agregar,
                'nombre'   => $nombre,
                'precio'   => $precio,
                'cantidad' => $cantidad_enviada
            ];
        }

        $resultado['status'] = true;
        echo json_encode($resultado);
        exit;
    }

    /* Eliminar producto del carrito */
    public function actioneliminar_lista($var = null)
    {
        $idProducto_para_eliminar = $_POST['id_producto'] ?? null;
        unset($_SESSION['carrito'][$idProducto_para_eliminar]);

        $resultado['status'] = true;
        echo json_encode($resultado);
        exit;
    }

    /* Método unificado de Checkout y procesamiento de compra */
    public function actionCheckout($var = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['USER']['id_usuario'] ?? $_SESSION['USER']['id'] ?? $_SESSION['usuario']->id_usuario ?? $_SESSION['usuario']->id ?? null;

        if (!$idUsuario) {
            static::path();
            header("Location: " . self::$path . "auth/login");
            exit();
        }

        $ruta = self::$path;

        // 1. SI EL USUARIO ENVÍA EL FORMULARIO DE COMPRA (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $direccion = trim($_POST['direccion'] ?? '');
            $medioPagoId = trim($_POST['medio_pago_id'] ?? 1);
            $carrito = $_SESSION['carrito'] ?? [];

            if (empty($carrito)) {
                static::path();
                header("Location: " . self::$path . "carrito/index");
                exit();
            }

            $total = 0;
            foreach ($carrito as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            // Inserción en la tabla 'pedidos' (Usando id_usuario e id_pago reales)
            $sqlPedido = "INSERT INTO pedidos (id_usuario, id_pago, direccion, total, fecha) 
                          VALUES (:id_usuario, :id_pago, :direccion, :total, NOW())";
            
            DataBase::execute($sqlPedido, [
                ':id_usuario'  => $idUsuario,
                ':id_pago'     => $medioPagoId,
                ':direccion'   => $direccion,
                ':total'       => $total
            ]);

            // Obtener el ID del pedido recién insertado
            $db = DataBase::connect();
            $idPedido = $db->lastInsertId();

            // Inserción en la tabla 'detalle_producto' (Sincronizado con tus tablas nuevas)
            foreach ($carrito as $item) {
                $sqlDetalle = "INSERT INTO detalle_producto (pedido_id, producto_id, cantidad, subtotal) 
                               VALUES (:pedido_id, :producto_id, :cantidad, :subtotal)";
                
                DataBase::execute($sqlDetalle, [
                    ':pedido_id'   => $idPedido,
                    ':producto_id' => $item['id'],
                    ':cantidad'    => $item['cantidad'],
                    ':subtotal'    => $item['precio'] * $item['cantidad']
                ]);
            }

            // Vaciar el carrito tras el éxito
            unset($_SESSION['carrito']);

            // Renderizar vista de éxito
            $datos = ["titulo" => "Pedido Exitoso"];
            $parametros_de_vista = [
                "head" => SiteController::head(),
                "topbar" => SiteController::topbar(),
                "datos" => $datos,
                "ruta" => $ruta
            ];

            Response::render($this->viewDir(__NAMESPACE__), 'exito', $parametros_de_vista);
            return;
        }

        // 2. PETICIÓN NORMAL (GET): Mostrar el formulario de checkout
        $datos = ["titulo" => "Finalizar Compra"];
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "topbar" => SiteController::topbar(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render($this->viewDir(__NAMESPACE__), 'checkout', $parametros_de_vista);
    }
}