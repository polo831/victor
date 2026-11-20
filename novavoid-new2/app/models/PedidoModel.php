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
}
