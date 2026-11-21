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

    public static function obtenerProductosConStock()
    {
        $sql = "
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
        ";
        return DataBase::query($sql);
    }

    public static function obtenerConStockPorId($id)
    {
        $sql = "
            SELECT
                p.*,
                s.cantidad AS stock
            FROM productos p
            LEFT JOIN stock s
                ON p.id_producto = s.producto_id
            WHERE p.id_producto = :id
            LIMIT 1
        ";
        $resultado = DataBase::query($sql, [':id' => $id]);
        return is_array($resultado) && isset($resultado[0]) ? $resultado[0] : null;
    }

    public static function existeProducto($id)
    {
        $sql = "SELECT id_producto FROM productos WHERE id_producto = :id LIMIT 1";
        $result = DataBase::query($sql, [':id' => $id]);
        return !empty($result);
    }

    public static function guardarProductoConStock($datos, $id = null)
    {
        $nombre = $datos['nombre'];
        $precio = $datos['precio'];
        $idCategoria = $datos['id_categoria'];
        $stockCantidad = $datos['stock'];
        $imagen = $datos['imagen'] ?? null;

        if ($id !== null) {
            // Actualización
            if ($imagen !== null) {
                $sql = "UPDATE productos
                        SET nombre_prenda = :nombre,
                            precio = :precio,
                            id_categoria = :id_categoria,
                            imagen = :imagen
                        WHERE id_producto = :id";
                $params = [
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':id_categoria' => $idCategoria,
                    ':imagen' => $imagen,
                    ':id' => $id
                ];
            } else {
                $sql = "UPDATE productos
                        SET nombre_prenda = :nombre,
                            precio = :precio,
                            id_categoria = :id_categoria
                        WHERE id_producto = :id";
                $params = [
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':id_categoria' => $idCategoria,
                    ':id' => $id
                ];
            }
            DataBase::execute($sql, $params);

            // Actualización o inserción de stock
            $checkStock = DataBase::query("SELECT id FROM stock WHERE producto_id = :id", [':id' => $id]);
            if (!empty($checkStock)) {
                DataBase::execute("UPDATE stock SET cantidad = :cantidad WHERE producto_id = :id", [
                    ':cantidad' => $stockCantidad,
                    ':id' => $id
                ]);
            } else {
                DataBase::execute("INSERT INTO stock (producto_id, cantidad) VALUES (:id, :cantidad)", [
                    ':id' => $id,
                    ':cantidad' => $stockCantidad
                ]);
            }
            return $id;
        } else {
            // Creación
            DataBase::execute(
                "INSERT INTO productos (nombre_prenda, precio, id_categoria, imagen)
                 VALUES (:nombre, :precio, :id_categoria, :imagen)",
                [
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':id_categoria' => $idCategoria,
                    ':imagen' => $imagen ?? 'no-image.png'
                ]
            );
            $res = DataBase::query("SELECT LAST_INSERT_ID() AS id_producto");
            $productoId = is_array($res) ? ($res[0]->id_producto ?? null) : ($res->id_producto ?? null);

            if ($productoId) {
                DataBase::execute(
                    "INSERT INTO stock (producto_id, cantidad) VALUES (:id, :cantidad)",
                    [':id' => $productoId, ':cantidad' => $stockCantidad]
                );
            }
            return $productoId;
        }
    }

    public static function eliminarProductoConStock($id)
    {
        DataBase::execute("DELETE FROM stock WHERE producto_id = :id", [':id' => $id]);
        return DataBase::execute("DELETE FROM productos WHERE id_producto = :id", [':id' => $id]);
    }
}