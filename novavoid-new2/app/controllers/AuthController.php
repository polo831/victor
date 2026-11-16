<?php

namespace app\controllers;

use \Controller;
use \Response;
use \DataBase;
use app\models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    // Constructor
    public function __construct()
    {
        self::$sessionStatus = SessionController::sessionVerificacion();
    }

    /*iniciar sesion*/
    public function actionLogin() {
        /*para que no vuelva a iniciar sesion*/
        if (self::$sessionStatus === "Online") {
            static::path();
            $ruta = self::$path.'home';
            header("Location: $ruta");
            exit();
        }
        $error = null;
        $mensajeExito = null;
        if (isset($_GET['registrado'])) {
            $mensajeExito = "¡Registro exitoso! Ya puedes iniciar sesión.";
        } elseif (isset($_GET['recuperado'])) {
            $mensajeExito = "¡Contraseña actualizada! Te enviamos un correo de confirmación.";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($email) && !empty($password)) {
                $usuario = UserModel::login($email, $password);
                if ($usuario) {
                    /*guarda todos los datos de sesion*/
                    $logueo = SessionController::setSessionData($email);

                    if ($logueo === true) {
                        static::path();
                        $ruta = self::$path.'home';
                        header("Location: $ruta");
                        exit();
                    }
                } else {
                    $error = "Correo electrónico o contraseña incorrectos.";
                }
            } else {
                $error = "Por favor completa todos los campos.";
            }
        }

        static::path();
        $nombre_de_archivoDeVista = 'login';
        $parametros_de_vista = [
            "head" => SiteController::head(),
            "header" => SiteController::header(),
            "topbar" => SiteController::topbar(),
            "menu" => SiteController::menu(),
            "menu_res" => SiteController::menu_res(),
            "error" => $error,
            "mensajeExito" => $mensajeExito,
            "ruta" => self::$path,
        ];
        
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }

    /*registrar*/
    public function actionRegister() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            if (!empty($nombre) && !empty($email) && !empty($password) && !empty($confirm_password) && !empty($direccion) && !empty($telefono)) {
                
                // 1. Validar que las contraseñas coincidan
                if ($password !== $confirm_password) {
                    $error = "Las contraseñas no coinciden.";
                } else {
                    // 2. Validar requisitos de seguridad (Mínimo 8 caracteres, 1 mayúscula, 1 número, 1 símbolo)
                    $patron_seguridad = '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
                    if (!preg_match($patron_seguridad, $password)) {
                        $error = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, un número y un símbolo especial.";
                    } else {
                        // 3. Registrar en el modelo
                        $exito = UserModel::registrar($nombre, $email, $password, $direccion, $telefono);

                        if ($exito) {
                            static::path();
                            $ruta = self::$path.'auth/login?registrado=1';
                            header("Location: $ruta");
                            exit();
                        } else {
                            $error = "El correo electrónico ya está registrado o hubo un error.";
                        }
                    }
                }
            } else {
                $error = "Por favor completa todos los campos.";
            }
        }

        static::path();
        $nombre_de_archivoDeVista = 'registro';
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

    /*cerrar sesion*/
    public function actionLogout(){
        session_destroy();
        static::path();
        $ruta = self::$path.'auth/login/';
        $_SESSION['SESSION']['STATUS'] = false;
        header("Location: $ruta");
        exit();
    }

   /*recuperar contraseña*/
    public function actionProcesarRecuperar() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $nueva_password = $_POST['nueva_password'] ?? '';
            $confirmar_password = $_POST['confirmar_password'] ?? '';

            if ($nueva_password !== $confirmar_password) {
                $error = "Las contraseñas no coinciden.";
            } else {
                $patron_seguridad = '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
                if (!preg_match($patron_seguridad, $nueva_password)) {
                    $error = "La nueva contraseña no cumple con los requisitos de seguridad.";
                } else {
                    // Verificar que el correo exista antes de actualizar y mandar el mail
                    $usuarioExiste = UserModel::findEmail($email);

                    if (!$usuarioExiste) {
                        $error = "No existe ninguna cuenta registrada con ese correo electrónico.";
                    } else {
                        $passwordHash = password_hash($nueva_password, PASSWORD_DEFAULT);

                        // Actualizar contraseña en el modelo
                        UserModel::actualizarPasswordPorEmail($email, $passwordHash);

                        // --- ENVÍO DE CORREO CON PHPMAILER ---
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'voidnova04@gmail.com';
                            $mail->Password   = 'kjrybizdawrdfjdc';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;
                            $mail->CharSet    = 'UTF-8';

                            $mail->setFrom('voidnova04@gmail.com', 'Nova Void Store');
                            $mail->addAddress($email);

                            $mail->isHTML(true);
                            $mail->Subject = 'Recuperación de contraseña - Nova Void Store';
                            $mail->Body    = "
                                <h2>Recuperación de Acceso</h2>
                                <p>Hola, tu contraseña en <strong>Nova Void Store</strong> fue actualizada correctamente.</p>
                                <p>Si no realizaste este cambio, comunícate con nosotros de inmediato.</p>
                            ";
                            $mail->AltBody = 'Tu contraseña en Nova Void Store fue actualizada correctamente.';

                            $mail->send();
                        } catch (Exception $e) {
                            // Si falla el envío, continúa sin frenar la redirección
                        }
                        // -------------------------------------

                        static::path();
                        $ruta = self::$path.'auth/login?recuperado=1';
                        header("Location: $ruta");
                        exit();
                    }
                }
            }
        }
        static::path();
        $nombre_de_archivoDeVista = 'recuperar';
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