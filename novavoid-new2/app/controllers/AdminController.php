<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;
use app\models\UserModel;


class AdminController extends Controller
{

    // Constructor
    public function __construct()
    {
        self::$sessionStatus = SessionController::sessionVerificacion();
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

        $productos = DataBase::query("
            SELECT
                p.*,
                s.cantidad AS stock,
                c.nombre_categoria AS categoria_nombre
            FROM productos p
            LEFT JOIN stock s
                ON p.id_producto = s.producto_id
            LEFT JOIN categorias c
                ON p.id_categoria = c.id_categoria
            ORDER BY p.id_producto DESC
        ");

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

    // 1. Obtener las categorías de la base de datos para el desplegable
    $categorias = DataBase::query("SELECT * FROM categorias");

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

            $resultado = DataBase::query("
                SELECT
                    p.*,
                    s.cantidad AS stock
                FROM productos p
                LEFT JOIN stock s
                    ON p.id_producto = s.producto_id
                WHERE p.id_producto = :id
            ", [':id' => $id]);

            $producto = $resultado[0] ?? null;
        }

        $categorias = DataBase::query("
            SELECT id_categoria, nombre_categoria
            FROM categorias
            ORDER BY nombre_categoria
        ");

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

        // Verificar categoría
        $categoria = DataBase::query(
            "SELECT id_categoria FROM categorias WHERE id_categoria = :id",
            [':id' => $idCategoria]
        );

        if (empty($categoria)) {
            die('La categoría seleccionada no existe.');
        }

        // Verificar producto si es una edición
        if ($id !== null) {

            $productoExistente = DataBase::query(
                "SELECT id_producto FROM productos WHERE id_producto = :id",
                [':id' => $id]
            );

            if (empty($productoExistente)) {
                die('El producto que intentás editar no existe.');
            }
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

        if ($id !== null) {

            // Actualizar producto
            if ($imagen !== null) {

                $sql = "
                    UPDATE productos
                    SET nombre_prenda = :nombre,
                        precio = :precio,
                        id_categoria = :id_categoria,
                        imagen = :imagen
                    WHERE id_producto = :id
                ";

                $params = [
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':id_categoria' => $idCategoria,
                    ':imagen' => $imagen,
                    ':id' => $id
                ];

            } else {

                $sql = "
                    UPDATE productos
                    SET nombre_prenda = :nombre,
                        precio = :precio,
                        id_categoria = :id_categoria
                    WHERE id_producto = :id
                ";

                $params = [
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':id_categoria' => $idCategoria,
                    ':id' => $id
                ];
            }

            DataBase::execute($sql, $params);

            // Verificar si ya existe un registro de stock
            $checkStock = DataBase::query(
                "SELECT id FROM stock WHERE producto_id = :id",
                [':id' => $id]
            );

            if (!empty($checkStock)) {

                DataBase::execute(
                    "UPDATE stock
                    SET cantidad = :cantidad
                    WHERE producto_id = :id",
                    [
                        ':cantidad' => $stockCantidad,
                        ':id' => $id
                    ]
                );

            } else {

                DataBase::execute(
                    "INSERT INTO stock (producto_id, cantidad)
                    VALUES (:id, :cantidad)",
                    [
                        ':id' => $id,
                        ':cantidad' => $stockCantidad
                    ]
                );
            }

        } else {

            // Crear producto
            DataBase::execute(
                "INSERT INTO productos
                    (nombre_prenda, precio, id_categoria, imagen)
                VALUES
                    (:nombre, :precio, :id_categoria, :imagen)",
                [
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':id_categoria' => $idCategoria,
                    ':imagen' => $imagen ?? 'no-image.png'
                ]
            );

            // Obtener el ID del último producto insertado
            $nuevoProducto = DataBase::query(
                "SELECT LAST_INSERT_ID() AS id_producto"
            );

            $productoId = $nuevoProducto[0]->id_producto ?? null;

            if ($productoId) {

                DataBase::execute(
                    "INSERT INTO stock (producto_id, cantidad)
                    VALUES (:id, :cantidad)",
                    [
                        ':id' => $productoId,
                        ':cantidad' => $stockCantidad
                    ]
                );
            }
        }

        header("Location: " . self::$path . "index.php?url=admin/productos");
        exit();
    }
   
    public function actionEliminarProducto()
    {
        SessionController::onlyUsers();
        static::path();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($id !== null && $id > 0) {

            // Primero eliminamos el stock asociado
            DataBase::execute(
                "DELETE FROM stock WHERE producto_id = :id",
                [':id' => $id]
            );

            // Después eliminamos el producto
            DataBase::execute(
                "DELETE FROM productos WHERE id_producto = :id",
                [':id' => $id]
            );
        }

        header("Location: " . self::$path . "index.php?url=admin/productos");
        exit();
    }

   /* 1. Listar usuarios */
    public function actionUsuarios()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        $usuarios = DataBase::query("SELECT * FROM usuarios ORDER BY id DESC");

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
            $resultado = DataBase::query("SELECT * FROM usuarios WHERE id = :id", [':id' => $id]);
            $usuario = $resultado[0] ?? null;
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

    /* 4. Guardar o actualizar usuario (La que ya tenías o adaptada a tus campos) */
    public function actionGuardarUsuario()
    {
        SessionController::onlyUsers();
        static::path();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $rol = trim($_POST['rol'] ?? 'cliente');

            if (!empty($id)) {
                if (!empty($password)) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password, direccion = :direccion, telefono = :telefono, rol = :rol WHERE id = :id";
                    DataBase::execute($sql, [':nombre' => $nombre, ':email' => $email, ':password' => $passwordHash, ':direccion' => $direccion, ':telefono' => $telefono, ':rol' => $rol, ':id' => $id]);
                } else {
                    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, direccion = :direccion, telefono = :telefono, rol = :rol WHERE id = :id";
                    DataBase::execute($sql, [':nombre' => $nombre, ':email' => $email, ':direccion' => $direccion, ':telefono' => $telefono, ':rol' => $rol, ':id' => $id]);
                }
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (nombre, email, password, direccion, telefono, rol) VALUES (:nombre, :email, :password, :direccion, :telefono, :rol)";
                DataBase::execute($sql, [
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':password' => $passwordHash,
                    ':direccion' => $direccion,
                    ':telefono' => $telefono,
                    ':rol' => $rol
                ]);
            }

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
            DataBase::execute("DELETE FROM usuarios WHERE id = :id", [':id' => $id]);
        }

        header("Location: " . self::$path . "public/index.php?url=admin/usuarios");
        exit();
    }

    /* 1. Listar todos los pedidos */

    public function actionPedidos()
    {
        SessionController::onlyUsers();
        $ruta = self::$path;

        $sql = "SELECT p.*, p.id_pedido as id, u.nombre as cliente_nombre, m.nombre as medio_pago_nombre 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.id_usuario = u.id 
                LEFT JOIN medios_pago m ON p.id_pago = m.id 
                ORDER BY p.id_pedido DESC";
        $pedidos = DataBase::query($sql);

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
            // 1. Datos principales del pedido, cliente y medio de pago (usando p.id_pedido y p.id_pago)
            $sqlPedido = "SELECT p.*, p.id_pedido as id, p.direccion as cliente_direccion, u.nombre as cliente_nombre, u.email as cliente_email, m.nombre as medio_pago_nombre 
                          FROM pedidos p 
                          LEFT JOIN usuarios u ON p.id_usuario = u.id 
                          LEFT JOIN medios_pago m ON p.id_pago = m.id 
                          WHERE p.id_pedido = :id";
            $resPedido = DataBase::query($sqlPedido, [':id' => $pedido_id]);
            $pedido = $resPedido[0] ?? null;

            // 2. Productos que componen el detalle
            // 2. Productos que componen el detalle
               $sqlDetalle = "SELECT dp.*, pr.nombre_prenda as producto_nombre, pr.precio as producto_precio 
               FROM detalle_producto dp 
               LEFT JOIN productos pr ON dp.producto_id = pr.id 
               WHERE dp.pedido_id = :pedido_id";
               $detalles = DataBase::query($sqlDetalle, [':pedido_id' => $pedido_id]);
           
        
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
                // 1. Actualizar el estado en la base de datos
                $sql = "UPDATE pedidos SET estado = :estado WHERE id = :id";
                DataBase::execute($sql, [':estado' => $nuevo_estado, ':id' => $pedido_id]);

                // 2. Obtener los datos del cliente y su email para enviarle el correo
                $sqlCliente = "SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email 
                               FROM pedidos p 
                               LEFT JOIN usuarios u ON p.usuario_id = u.id 
                               WHERE p.id = :id";
                $resultado = DataBase::query($sqlCliente, [':id' => $pedido_id]);
                $pedidoInfo = $resultado[0] ?? null;

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


