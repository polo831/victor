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
public function actionLogin() {
        /* 1. Si ya tiene sesión iniciada, redirigir según su rol */
        if (self::$sessionStatus === "Online") {
            static::path();
            // Verifica cómo guarda el rol tu SessionController (ej. $_SESSION['USER']['rol'] o $_SESSION['rol'])
            $rolSesion = $_SESSION['USER']['rol'] ?? $_SESSION['rol'] ?? null;
            
            if ($rolSesion == 1 || $rolSesion === 'admin') {
                $ruta = self::$path . 'admin/panel';
            } else {
                $ruta = self::$path . 'home';
            }
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
                    $logueo = SessionController::setSessionData($email);

                    if ($logueo === true) {
                        static::path();

                        // 2. Apuntar exactamente a 'admin/panel'
                        if (isset($usuario->rol) && ($usuario->rol == 1 || $usuario->rol === 'admin')) {
                            $ruta = self::$path . 'admin/panel';
                        } else {
                            $ruta = self::$path . 'home';
                        }

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
        // ... resto de tu vista login ...


   
        

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

    public function actionRecuperar() {
        return $this->actionProcesarRecuperar();
    }

    /*recuperar contraseña en 2 pasos: 1. Envío de código OTP por email; 2. Validación de código y cambio de contraseña*/
    public function actionProcesarRecuperar() {
        // Reiniciar proceso si se solicita
        if (isset($_GET['reiniciar'])) {
            unset($_SESSION['recuperar_codigo']);
            static::path();
            header("Location: " . self::$path . "auth/procesarRecuperar");
            exit();
        }

        $error = null;
        $mensajeExito = null;
        $paso = 1;
        $emailRecuperar = '';

        // Comprobar si ya hay un código solicitado en sesión y no ha expirado
        if (!empty($_SESSION['recuperar_codigo']) && !empty($_SESSION['recuperar_codigo']['email'])) {
            if (time() > $_SESSION['recuperar_codigo']['expira']) {
                unset($_SESSION['recuperar_codigo']);
                $error = "El código de verificación ha expirado. Por favor solicita uno nuevo.";
                $paso = 1;
            } else {
                $paso = 2;
                $emailRecuperar = $_SESSION['recuperar_codigo']['email'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST['accion'] ?? '';

            // --- PASO 1: Solicitar código ---
            if ($accion === 'solicitar_codigo' || (isset($_POST['email']) && !isset($_POST['codigo']))) {
                $email = trim($_POST['email'] ?? '');

                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Por favor ingresa un correo electrónico válido.";
                    $paso = 1;
                } else {
                    $usuarioExiste = UserModel::findEmail($email);

                    if (!$usuarioExiste) {
                        $error = "No existe ninguna cuenta registrada con ese correo electrónico.";
                        $paso = 1;
                    } else {
                        // Generar código numérico de 6 dígitos
                        $codigo = str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                        $_SESSION['recuperar_codigo'] = [
                            'email' => $email,
                            'codigo' => $codigo,
                            'expira' => time() + 900 // 15 minutos de vigencia
                        ];

                        // Enviar el correo con PHPMailer
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
                            $mail->Subject = 'Código de verificación - Nova Void Store';
                            $mail->Body    = "
                                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 25px; border: 1px solid #e5e7eb; border-radius: 12px; background-color: #ffffff;'>
                                    <h2 style='color: #111827; text-align: center; margin-bottom: 10px;'>Nova Void Store</h2>
                                    <h3 style='color: #374151; text-align: center; font-weight: normal; margin-top: 0;'>Recuperación de Contraseña</h3>
                                    <p style='color: #4b5563; font-size: 14px;'>Hola,</p>
                                    <p style='color: #4b5563; font-size: 14px;'>Has solicitado restablecer tu contraseña. Utiliza el siguiente código de 6 dígitos para verificar tu identidad y crear tu nueva clave:</p>
                                    <div style='text-align: center; margin: 25px 0;'>
                                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; background-color: #f3f4f6; padding: 12px 24px; border-radius: 8px; display: inline-block; color: #111827; font-family: monospace;'>{$codigo}</span>
                                    </div>
                                    <p style='color: #6b7280; font-size: 13px;'>Este código expirará en <strong>15 minutos</strong>. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                                    <p style='color: #9ca3af; font-size: 11px; text-align: center; margin: 0;'>Nova Void Store - Todos los derechos reservados.</p>
                                </div>
                            ";
                            $mail->AltBody = "Tu código de verificación para Nova Void Store es: {$codigo}. Válido por 15 minutos.";

                            $mail->send();
                            $mensajeExito = "Se ha enviado un código de verificación de 6 dígitos a tu correo.";
                            $paso = 2;
                            $emailRecuperar = $email;
                        } catch (Exception $e) {
                            unset($_SESSION['recuperar_codigo']);
                            $error = "No se pudo enviar el correo de verificación. Por favor intenta nuevamente.";
                            $paso = 1;
                        }
                    }
                }
            }
            // --- PASO 2: Confirmar código y cambiar contraseña ---
            elseif ($accion === 'confirmar_codigo' || isset($_POST['codigo'])) {
                if (empty($_SESSION['recuperar_codigo']) || empty($_SESSION['recuperar_codigo']['codigo'])) {
                    $error = "La sesión de recuperación no es válida o expiró. Por favor solicita un nuevo código.";
                    $paso = 1;
                } elseif (time() > $_SESSION['recuperar_codigo']['expira']) {
                    unset($_SESSION['recuperar_codigo']);
                    $error = "El código ha expirado. Por favor solicita uno nuevo.";
                    $paso = 1;
                } else {
                    $codigoIngresado = trim($_POST['codigo'] ?? '');
                    $nueva_password = $_POST['nueva_password'] ?? '';
                    $confirmar_password = $_POST['confirmar_password'] ?? '';

                    if ($codigoIngresado !== $_SESSION['recuperar_codigo']['codigo']) {
                        $error = "El código de verificación ingresado es incorrecto.";
                        $paso = 2;
                        $emailRecuperar = $_SESSION['recuperar_codigo']['email'];
                    } elseif ($nueva_password !== $confirmar_password) {
                        $error = "Las contraseñas no coinciden.";
                        $paso = 2;
                        $emailRecuperar = $_SESSION['recuperar_codigo']['email'];
                    } else {
                        $patron_seguridad = '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
                        if (!preg_match($patron_seguridad, $nueva_password)) {
                            $error = "La nueva contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un símbolo especial.";
                            $paso = 2;
                            $emailRecuperar = $_SESSION['recuperar_codigo']['email'];
                        } else {
                            $email = $_SESSION['recuperar_codigo']['email'];
                            $passwordHash = password_hash($nueva_password, PASSWORD_DEFAULT);

                            // Actualizar contraseña a través del modelo
                            UserModel::actualizarPasswordPorEmail($email, $passwordHash);
                            unset($_SESSION['recuperar_codigo']);

                            // Enviar confirmación por correo
                            try {
                                $mail = new PHPMailer(true);
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
                                $mail->Subject = 'Contraseña actualizada - Nova Void Store';
                                $mail->Body    = "
                                    <h2>Contraseña Actualizada</h2>
                                    <p>Hola, tu contraseña en <strong>Nova Void Store</strong> fue actualizada exitosamente mediante el código de verificación.</p>
                                    <p>Si no realizaste este cambio, comunícate con nosotros de inmediato.</p>
                                ";
                                $mail->AltBody = 'Tu contraseña en Nova Void Store fue actualizada exitosamente.';

                                $mail->send();
                            } catch (Exception $e) {
                                // Continuar si falla el correo informativo secundario
                            }

                            static::path();
                            $ruta = self::$path . 'auth/login?recuperado=1';
                            header("Location: $ruta");
                            exit();
                        }
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
            "mensajeExito" => $mensajeExito,
            "paso" => $paso,
            "emailRecuperar" => $emailRecuperar,
            "ruta" => self::$path,
        ];
        Response::render($this->viewDir(__NAMESPACE__), $nombre_de_archivoDeVista, $parametros_de_vista);
    }
}