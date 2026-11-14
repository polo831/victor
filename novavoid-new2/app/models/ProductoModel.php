<?php

namespace app\models;

use \DataBase;
use \Model;

class ProductoModel extends Model
{
    protected $table = "productos";
    protected $primaryKey = "id_producto"; // Actualizado a id_producto

    public static function obtenerTodos() {
        $model = new static();

        $sql = "SELECT * FROM productos";
        $result = DataBase::query($sql);

        return $result;
    }

    public static function obtenerPorCategoria($id_categoria)
    {
        $model = new static();
        // Corregido de categoria_id a id_categoria[cite: 1]
        $sql = "SELECT * FROM productos WHERE id_categoria = :id_categoria";
        $params = [':id_categoria' => $id_categoria];
        $result = DataBase::query($sql, $params);

        return $result;
    }

    public static function obtenerPorId($id)
    {
        $sql = "SELECT * FROM productos WHERE id_producto = :id LIMIT 1";
        $params = [':id' => $id];
        $result = DataBase::query($sql, $params);

        return is_array($result) && isset($result[0]) ? $result[0] : $result;
    }

    public static function buscarPorNombre($termino)
    {
        $sql = "SELECT * FROM productos WHERE nombre_prenda LIKE :termino OR descripcion LIKE :termino";
        $params = [':termino' => '%' . $termino . '%'];
        return DataBase::query($sql, $params);
    }
}