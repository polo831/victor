<?php

namespace app\models;

use \DataBase;
use \Model;

class PedidoModel extends Model
{
    protected $table = "pedidos";
    protected $primaryKey = "id_pedido";

    /**
     * Inserta un nuevo pedido en la base de datos y retorna su ID generado
     */
    public static function crearPedido($idUsuario, $idPago, $direccion, $total)
    {
        $sql = "INSERT INTO pedidos (id_usuario, id_pago, direccion, total, fecha) 
                VALUES (:id_usuario, :id_pago, :direccion, :total, NOW())";
        
        DataBase::execute($sql, [
            ':id_usuario' => $idUsuario,
            ':id_pago'    => $idPago,
            ':direccion'  => $direccion,
            ':total'      => $total
        ]);

        $resId = DataBase::query("SELECT LAST_INSERT_ID() AS id");
        return is_array($resId) ? ($resId[0]->id ?? $resId[0]['id'] ?? 0) : ($resId->id ?? 0);
    }

    /**
     * Inserta un ítem individual en la tabla de detalle_producto
     */
    public static function agregarDetalle($idPedido, $idProducto, $talle, $color, $cantidad, $subtotal)
    {
        $sql = "INSERT INTO detalle_producto (pedido_id, producto_id, talle, color, cantidad, subtotal) 
                VALUES (:pedido_id, :producto_id, :talle, :color, :cantidad, :subtotal)";
        
        return DataBase::execute($sql, [
            ':pedido_id'   => $idPedido,
            ':producto_id' => $idProducto,
            ':talle'       => $talle,
            ':color'       => $color,
            ':cantidad'    => $cantidad,
            ':subtotal'    => $subtotal
        ]);
    }

    /**
     * Registra un pedido completo (cabecera y cada detalle) dentro del modelo
     */
    public static function registrarPedidoCompleto($idUsuario, $idPago, $direccion, array $carrito)
    {
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $idPedido = self::crearPedido($idUsuario, $idPago, $direccion, $total);

        if ($idPedido > 0) {
            foreach ($carrito as $item) {
                self::agregarDetalle(
                    $idPedido,
                    $item['id'],
                    $item['talle'] ?? null,
                    $item['color'] ?? null,
                    $item['cantidad'],
                    $item['precio'] * $item['cantidad']
                );
            }
        }

        return $idPedido;
    }

    public static function obtenerTodosAdmin()
    {
        $sql = "SELECT p.*, p.id_pedido as id, u.nombre as cliente_nombre, m.nombre as medio_pago_nombre 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.id_usuario = u.id 
                LEFT JOIN medios_pago m ON p.id_pago = m.id 
                ORDER BY p.id_pedido DESC";
        return DataBase::query($sql);
    }

    public static function obtenerPedidoConCliente($id)
    {
        $sql = "SELECT p.*, p.id_pedido as id, p.direccion as cliente_direccion, u.nombre as cliente_nombre, u.email as cliente_email, m.nombre as medio_pago_nombre 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.id_usuario = u.id 
                LEFT JOIN medios_pago m ON p.id_pago = m.id 
                WHERE p.id_pedido = :id";
        $res = DataBase::query($sql, [':id' => $id]);
        return is_array($res) && isset($res[0]) ? $res[0] : null;
    }

    public static function obtenerDetallesPorPedidoId($idPedido)
    {
        $sql = "SELECT dp.*, pr.nombre_prenda as producto_nombre, pr.precio as producto_precio 
                FROM detalle_producto dp 
                LEFT JOIN productos pr ON dp.producto_id = pr.id_producto
                WHERE dp.pedido_id = :pedido_id";
        return DataBase::query($sql, [':pedido_id' => $idPedido]);
    }

    public static function actualizarEstado($idPedido, $nuevoEstado)
    {
        $sql = "UPDATE pedidos SET estado = :estado WHERE id_pedido = :id";
        return DataBase::execute($sql, [':estado' => $nuevoEstado, ':id' => $idPedido]);
    }
}
