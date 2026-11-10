<?php

namespace app\controllers;

use Controller;
use Response;
use DataBase;
use app\models\UserModel;

class PerfilController extends Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function actionIndex($var = null)
    {
        // Priorizamos 'id' que es el nombre real de la columna en tu BD
        $idUsuario = $_SESSION['USER']['id'] ?? $_SESSION['USER']['id_usuario'] ?? null;

        if (!$idUsuario) {
            header("Location: /novavoid-new2/public/auth/login");
            exit();
        }

        $ruta = self::$path;
        $usuario = UserModel::obtenerPorId($idUsuario);

        $datos = [
            "titulo" => "Mi Perfil",
            "usuario" => $usuario
        ];

        $head = SiteController::head();
        $topbar = SiteController::topbar();

        require_once APP_PATH . "views/auth/perfil.php";
    }

    public function actionActualizar($var = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_SESSION['USER']['id'] ?? $_SESSION['USER']['id_usuario'] ?? null;

            if (!$idUsuario) {
                header("Location: /novavoid-new2/public/auth/login");
                exit();
            }

            $nombre = trim($_POST['nombre'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            $actualizado = UserModel::actualizarPerfil($idUsuario, $nombre, $direccion, $telefono);

            if ($actualizado) {
                if (isset($_SESSION['USER'])) {
                    $_SESSION['USER']['name'] = $nombre;
                }
                $exito = "¡Tus datos han sido actualizados con éxito!";
            } else {
                $error = "Ocurrió un error al actualizar los datos.";
            }

            $ruta = self::$path;
            $usuario = UserModel::obtenerPorId($idUsuario);

            $datos = [
                "titulo" => "Mi Perfil",
                "usuario" => $usuario,
                "exito" => $exito ?? null,
                "error" => $error ?? null
            ];

            $head = SiteController::head();
            $topbar = SiteController::topbar();

            require_once APP_PATH . "views/auth/perfil.php";
        }
    }
}