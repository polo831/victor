<?php

namespace app\models;

use \DataBase;
use \Model;

class UserModel extends Model
{
    protected $table = "usuarios";
    protected $primaryKey = "id";
    protected $secundaryKey = "email";
    public $email;

    public static function getAllUsers()
    {
        $model = new static();
        $sql = "SELECT * FROM usuarios";
        $result = DataBase::query($sql);

        return $result;
    }

    public static function findEmail($email)
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} WHERE {$model->secundaryKey} = :email";
        $params = ["email" => $email];
        $result = DataBase::getRecord($sql, $params);
        return $result;
    }

    public static function getUserByEmail($email)
    {
        $model = new static();
        $sql = "SELECT * FROM " . $model->table . " WHERE email = '$email'";
        $result = DataBase::query($sql);

        return $result;
    }

    public static function login($email, $password)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $params = [':email' => $email];
        
        $result = DataBase::query($sql, $params);

        $usuario = is_array($result) && isset($result[0]) ? $result[0] : $result;

        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario;
        }

        return false;
    }

    // Nueva función exclusiva para el login del Administrador
    public static function loginAdmin($email, $password)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND rol = 1 LIMIT 1";
        $params = [':email' => $email];
        
        $result = DataBase::query($sql, $params);

        $usuario = is_array($result) && isset($result[0]) ? $result[0] : $result;

        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario;
        }

        return false;
    }

    public static function registrar($nombre, $email, $password, $direccion, $telefono) 
    {
        // Hashear la contraseña antes de guardarla
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // 1. Verificar si el correo ya existe usando tu wrapper
        $sqlCheck = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
        $paramsCheck = [':email' => $email];
        $resultCheck = DataBase::query($sqlCheck, $paramsCheck);

        // Si devuelve resultados, significa que el email ya está registrado
        $existe = is_array($resultCheck) && isset($resultCheck[0]) ? $resultCheck[0] : $resultCheck;
        if ($existe) {
            return false; // El correo ya existe
        }

        // 2. Insertar el nuevo usuario usando DataBase::execute
        $sqlInsert = "INSERT INTO usuarios (nombre, email, password, direccion, telefono, rol) 
                      VALUES (:nombre, :email, :password, :direccion, :telefono, 'cliente')";
        
        $paramsInsert = [
            ':nombre'    => $nombre,
            ':email'     => $email,
            ':password'  => $passwordHash,
            ':direccion' => $direccion,
            ':telefono'  => $telefono
        ];

        return DataBase::execute($sqlInsert, $paramsInsert);
    }

    // Obtener usuario por su ID
    public static function obtenerPorId($id)
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} WHERE {$model->primaryKey} = :id LIMIT 1";
        $params = [':id' => $id];
        
        $result = DataBase::getRecord($sql, $params);
        
        $usuario = is_array($result) && isset($result[0]) ? $result[0] : $result;
        return $usuario;
    }

    // Actualizar los datos del perfil
    public static function actualizarPerfil($id, $nombre, $direccion, $telefono)
    {
        $model = new static();
        $sql = "UPDATE {$model->table} SET nombre = :nombre, direccion = :direccion, telefono = :telefono WHERE {$model->primaryKey} = :id";
        
        $params = [
            ':nombre'    => $nombre,
            ':direccion' => $direccion,
            ':telefono'  => $telefono,
            ':id'        => $id
        ];
        
        return DataBase::execute($sql, $params);
    }

    // Actualizar contraseña al recuperar acceso por correo electrónico
    public static function actualizarPasswordPorEmail($email, $passwordHash)
    {
        $model = new static();
        $sql = "UPDATE {$model->table} SET password = :password WHERE {$model->secundaryKey} = :email";
        
        $params = [
            ':password' => $passwordHash,
            ':email'    => $email
        ];
        
        return DataBase::execute($sql, $params);
    }

    public static function obtenerTodosDesc()
    {
        return DataBase::query("SELECT * FROM usuarios ORDER BY id DESC");
    }

    public static function guardarUsuarioAdmin($datos, $id = null)
    {
        $nombre = $datos['nombre'];
        $email = $datos['email'];
        $password = $datos['password'] ?? '';
        $direccion = $datos['direccion'];
        $telefono = $datos['telefono'];
        $rol = $datos['rol'] ?? 'cliente';

        if (!empty($id)) {
            if (!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password, direccion = :direccion, telefono = :telefono, rol = :rol WHERE id = :id";
                return DataBase::execute($sql, [':nombre' => $nombre, ':email' => $email, ':password' => $passwordHash, ':direccion' => $direccion, ':telefono' => $telefono, ':rol' => $rol, ':id' => $id]);
            } else {
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, direccion = :direccion, telefono = :telefono, rol = :rol WHERE id = :id";
                return DataBase::execute($sql, [':nombre' => $nombre, ':email' => $email, ':direccion' => $direccion, ':telefono' => $telefono, ':rol' => $rol, ':id' => $id]);
            }
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, email, password, direccion, telefono, rol) VALUES (:nombre, :email, :password, :direccion, :telefono, :rol)";
            return DataBase::execute($sql, [
                ':nombre' => $nombre,
                ':email' => $email,
                ':password' => $passwordHash,
                ':direccion' => $direccion,
                ':telefono' => $telefono,
                ':rol' => $rol
            ]);
        }
    }

    public static function eliminarUsuario($id)
    {
        return DataBase::execute("DELETE FROM usuarios WHERE id = :id", [':id' => $id]);
    }
}