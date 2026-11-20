<?php 
namespace app\controllers;
use \Controller;
use app\models\UserModel;


class SessionController extends Controller
{
   
    private static function setSession(){
        #SC-01 Al primer ingreso de un usuario al sistema asigna un estado de FALSE a la variable de control STATUS
        if (!isset($_SESSION['SESSION']['STATUS'])) {
            $_SESSION['SESSION']['STATUS'] = false;
        }
    }

    public static function sessionVerificacion(){
        self::setSession();

        #SC-02 Devuelve el estado de session (Logueado o No) del usuario como un string "OnLine" o "OffLine"
        if ($_SESSION['SESSION']['STATUS']) {
            $status = 'Online';
        }else{
            $status = 'OffLine';
        }
        
        return $status;
    }

    public static function setSessionData($userEmailOrID){

        $usuario = UserModel::getUserByEmail($userEmailOrID);
        if (isset($usuario[0]->nombre) && isset($usuario[0]->email)  && isset($usuario[0]->id)) {
            $_SESSION['SESSION']['STATUS'] = true;
            $_SESSION['USER']['name']       = $usuario[0]->nombre;
            $_SESSION['USER']['mail']   = $usuario[0]->email;     
            $_SESSION['USER']['id_usuario'] = $usuario[0]->id;
            $_SESSION['USER']['rol'] = $usuario[0]->rol;
            $result = true;
        }else{
            $result = false;
        }

        return $result;
     }

     public static function getType(){
         return  $_SESSION['USER']['type'];
     }


    public static function onlyAdmins(){
        static::path();
        self::$sessionStatus = self::sessionVerificacion();
        $rolSesion = $_SESSION['USER']['rol'] ?? $_SESSION['rol'] ?? null;
        $esAdmin = ($rolSesion == 1 || $rolSesion === 'admin');

        // Si no está logueado o no es administrador, redirigir al login
        if (self::$sessionStatus !== 'Online' || !$esAdmin) {
            header("Location: " . self::$path . "auth/login");
            exit();
        }
        return true;
    }

    public static function onlyUsers(){
        static::path();
        $continuar = false;

        if(!isset(self::$sessionStatus)){
            echo 'Error en verificacion de sesiones';
        }  
    }

    public static function onlyLogin(){
        static::path();
        $continuar = false;

        if(!isset(self::$sessionStatus)){
            echo 'Error en verificacion de sesiones';
        }else{
            $ruta = self::$path.'account/login/';
            if (self::$sessionStatus === 'OffLine') {
                header("Location: $ruta");
            }elseif(self::$sessionStatus === 'Online'){
                $continuar = true;
            }
            return $continuar;
        }   
    }
}