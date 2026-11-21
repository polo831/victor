<?php

namespace app\controllers;

use \Controller;
use \Response;
use app\models\UserModel;
use app\models\ProductoModel;
use app\models\CategoriaModel;
use app\models\PedidoModel;


class AdminController extends Controller
{

    // Constructor
    public function __construct()
    {
        self::$sessionStatus = SessionController::sessionVerificacion();
        $action = $_GET['url'] ?? '';
        if (strpos($action, 'admin/login') === false && strpos($action, 'admin/loguin') === false) {
            SessionController::onlyAdmins();
        }
    }

    /*recibo el id del producto mediante el parametro*/
    public function actionpanel()
    {
        SessionController::onlyUsers();
        /*sirve para no usar tantos ../ */
        $ruta = self::$path;

        $datos = [
            "titulo" => "Panel de Administración",
            "mensaje" => "Bienvenido al panel de control de tu tienda."
        ];
        static::path();
        $nombre_de_archivoDeVista = 'panel';
       $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos"=>$datos,
            "ruta"=>$ruta
        ];
        
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

     /* Listar productos en la gestión (ABM) */
    public function actionProductos()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        $productos = ProductoModel::obtenerProductosConStock();

        $datos = [
            "titulo" => "Gestión de Productos",
            "productos" => $productos
        ];

        static::path();

        $nombre_de_archivoDeVista = 'productos';

        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render(
            $this->viewDir(__NAMESPACE__),
            $nombre_de_archivoDeVista,
            $parametros_de_vista
        );
    }

    /* Mostrar formulario para crear un producto nuevo */
   public function actionCrearProducto()
{
    SessionController::onlyUsers();
    $ruta = self::$path;

    // 1. Obtener las categorías de la base de datos para el desplegable desde CategoriaModel
    $categorias = CategoriaModel::obtenerTodasCategorias();

    $datos = [
        "titulo" => "Nuevo Producto",
        "categorias" => $categorias // <-- Esto es lo que alimenta el select de la vista
    ];

    static::path();
    $nombre_de_archivoDeVista = 'form_producto'; // o el nombre de tu vista de formulario
    
    $parametros_de_vista = [
        "head" => SiteController::head(),
        "header" => SiteController::header(),
        "topbar" => SiteController::topbar(),
        "menu" => SiteController::menu(),
        "menu_res" => SiteController::menu_res(),
        "datos" => $datos,
        "ruta" => $ruta
    ];

    Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
}

    /* Mostrar formulario para editar un producto existente */
    public function actionEditarProducto()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $producto = null;

        if ($id !== null && $id > 0) {
            $producto = ProductoModel::obtenerConStockPorId($id);
        }

        $categorias = CategoriaModel::obtenerTodasCategorias();

        $datos = [
            "titulo" => "Editar Producto",
            "producto" => $producto,
            "categorias" => $categorias
        ];

        static::path();

        $nombre_de_archivoDeVista = 'form_producto';

        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render(
            $this->viewDir(__NAMESPACE__),
            $nombre_de_archivoDeVista,
            $parametros_de_vista
        );
    }
   
    public function actionGuardarProducto()
    {
        SessionController::onlyUsers();
        static::path();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        $stockCantidad = (int)($_POST['stock'] ?? 0);
        $idCategoria = (int)($_POST['id_categoria'] ?? 0);

        // Validaciones
        if (
            $nombre === '' ||
            $precio <= 0 ||
            $stockCantidad < 0 ||
            $idCategoria <= 0
        ) {
            die('Todos los campos son obligatorios y deben contener valores válidos.');
        }

        // Verificar categoría desde el modelo CategoriaModel
        if (!CategoriaModel::existeCategoria($idCategoria)) {
            die('La categoría seleccionada no existe.');
        }

        // Verificar producto si es una edición desde el modelo ProductoModel
        if ($id !== null && !ProductoModel::existeProducto($id)) {
            die('El producto que intentás editar no existe.');
        }

        // Manejo de imagen
        $imagen = null;

        if (
            isset($_FILES['imagen']) &&
            $_FILES['imagen']['error'] === UPLOAD_ERR_OK
        ) {
            $nombreArchivo = time() . '_' . basename($_FILES['imagen']['name']);
            $destino = __DIR__ . '/../../public/img/' . $nombreArchivo;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen = $nombreArchivo;
            }
        }

        // Delegar creación o actualización y manejo de stock a ProductoModel
        ProductoModel::guardarProductoConStock([
            'nombre'       => $nombre,
            'precio'       => $precio,
            'id_categoria' => $idCategoria,
            'stock'        => $stockCantidad,
            'imagen'       => $imagen
        ], $id);

        header("Location: " . self::$path . "index.php?url=admin/productos");
        exit();
    }
   
    public function actionEliminarProducto()
    {
        SessionController::onlyUsers();
        static::path();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($id !== null && $id > 0) {
            // Delegar eliminación de stock y producto a ProductoModel
            ProductoModel::eliminarProductoConStock($id);
        }

        header("Location: " . self::$path . "index.php?url=admin/productos");
        exit();
    }

   /* 1. Listar usuarios */
    public function actionUsuarios()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        // Consulta delegada a UserModel
        $usuarios = UserModel::obtenerTodosDesc();

        $datos = [
            "titulo" => "Gestión de Usuarios",
            "usuarios" => $usuarios
        ];

        static::path();
        $nombre_de_archivoDeVista = 'usuarios';
        
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /* 2. Formulario para crear usuario */
    public function actionCrearUsuario()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        $datos = [
            "titulo" => "Nuevo Usuario"
        ];

        static::path();
        $nombre_de_archivoDeVista = 'form_usuario';
        
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /* 3. Formulario para editar usuario */
    public function actionEditarUsuario()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;
        $id = $_GET['id'] ?? null;
        $usuario = null;

        if ($id) {
            // Consulta delegada a UserModel
            $usuario = UserModel::obtenerPorId($id);
        }

        $datos = [
            "titulo" => "Editar Usuario",
            "usuario" => $usuario
        ];

        static::path();
        $nombre_de_archivoDeVista = 'form_usuario';
        
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /* 4. Guardar o actualizar usuario */
    public function actionGuardarUsuario()
    {
        SessionController::onlyUsers();
        static::path();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $datosUsuario = [
                'nombre'    => trim($_POST['nombre'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'password'  => $_POST['password'] ?? '',
                'direccion' => trim($_POST['direccion'] ?? ''),
                'telefono'  => trim($_POST['telefono'] ?? ''),
                'rol'       => trim($_POST['rol'] ?? 'cliente')
            ];

            // Inserción o actualización delegada a UserModel
            UserModel::guardarUsuarioAdmin($datosUsuario, $id);

            header("Location: " . self::$path . "public/index.php?url=admin/usuarios");
            exit();
        }
    }

    /* 5. Eliminar usuario */
    public function actionEliminarUsuario()
    {
        SessionController::onlyUsers();
        static::path();
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Eliminación delegada a UserModel
            UserModel::eliminarUsuario($id);
        }

        header("Location: " . self::$path . "public/index.php?url=admin/usuarios");
        exit();
    }

    /* 1. Listar todos los pedidos */
    public function actionPedidos()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        // Consulta delegada a PedidoModel
        $pedidos = PedidoModel::obtenerTodosAdmin();

        $datos = [
            "titulo" => "Gestión de Pedidos",
            "pedidos" => $pedidos
        ];

        static::path();
        $nombre_de_archivoDeVista = 'pedidos';
        
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }
   

    /* 2. Ver el detalle específico de un pedido */
    public function actionDetallePedido()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;
        $pedido_id = $_GET['id'] ?? null;

        $pedido = null;
        $detalles = [];

        if ($pedido_id) {
            // Consultas delegadas a PedidoModel
            $pedido = PedidoModel::obtenerPedidoConCliente($pedido_id);
            $detalles = PedidoModel::obtenerDetallesPorPedidoId($pedido_id);
        }

        $datos = [
            "titulo" => "Detalle del Pedido #" . ($pedido_id ?? ''),
            "pedido" => $pedido,
            "detalles" => $detalles
        ];

        static::path();
        $nombre_de_archivoDeVista = 'detallepedido';
        
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "datos" => $datos,
            "ruta" => $ruta
        ];

        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /* Actualizar estado del pedido y enviar correo de notificación */
    public function actionActualizarEstadoPedido()
    {
        SessionController::onlyUsers();
        static::path();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pedido_id = $_POST['pedido_id'] ?? null;
            $nuevo_estado = $_POST['estado'] ?? 'Pendiente';

            if ($pedido_id) {
                // 1. Actualizar el estado mediante PedidoModel
                PedidoModel::actualizarEstado($pedido_id, $nuevo_estado);

                // 2. Obtener los datos del cliente mediante PedidoModel
                $pedidoInfo = PedidoModel::obtenerPedidoConCliente($pedido_id);

                if ($pedidoInfo && !empty($pedidoInfo->cliente_email)) {
                    $para = $pedidoInfo->cliente_email;
                    $asunto = "Actualización de tu Pedido #" . $pedidoInfo->id;
                    $mensaje = "Hola " . $pedidoInfo->cliente_nombre . ",\n\n" .
                               "Te informamos que el estado de tu pedido #" . $pedidoInfo->id . " ha cambiado a: " . $nuevo_estado . ".\n\n" .
                               "Gracias por tu compra en nuestra tienda.";
                    $cabeceras = "From: no-reply@tu-tienda.com\r\n" .
                                 "Reply-To: soporte@tu-tienda.com\r\n" .
                                 "X-Mailer: PHP/" . phpversion();

                    // Enviar correo (Nota: En entorno local con XAMPP puede requerir configuración SMTP)
                    @mail($para, $asunto, $mensaje, $cabeceras);
                }
            }

            // Redirigir de vuelta al detalle del pedido
            header("Location: " . self::$path . "public/index.php?url=admin/detallePedido&id=" . $pedido_id);
            exit();
        }
    }

public function actionLogin() {
        /*para que no vuelva a iniciar sesion*/
        if (self::$sessionStatus === "Online") {
            static::path();
            $ruta = self::$path.'admin/panel';
            header("Location: $ruta");
            exit();
        }
        $error = null;
        $mensajeExito = isset($_GET['registrado']) ? "¡Registro exitoso! Ya puedes iniciar sesión." : null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($email) && !empty($password)) {
                // 1. Usamos el login normal para verificar correo y contraseña
                $usuario = UserModel::login($email, $password);
                
                if ($usuario) {
                    // 2. Validamos que sea administrador (por ejemplo, que su rol sea 1)
                    if (isset($usuario->rol) && $usuario->rol == 1) {
                        
                        /*guarda todos los datos de sesion*/
                        $logueo = SessionController::setSessionData($email);

                        if ($logueo === true) {
                            static::path();
                            $ruta = self::$path.'admin/panel';
                            header("Location: $ruta");
                            exit();
                        }
                    } else {
                        // Si se logueó pero es un cliente común (rol != 1)
                        $error = "Acceso denegado: Esta cuenta no tiene permisos de administrador.";
                    }
                } else {
                    $error = "Correo electrónico o contraseña incorrectos.";
                }
            } else {
                $error = "Por favor completa todos los campos.";
            }
        }

        static::path();
        $nombre_de_archivoDeVista = 'loguin';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "error" => $error,
            "ruta" => self::$path,
        ];
        
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }
}


