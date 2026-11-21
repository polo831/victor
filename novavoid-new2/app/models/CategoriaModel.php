<?php

namespace app\models;

use \DataBase;
use \Model;

class CategoriaModel extends Model
{
	protected $table = "categorias";
	protected $primaryKey = "id";


	public static function obtenerTodasCategorias() {
        $model = new static();

        /*falta estado en bd*/
		$sql = "SELECT * FROM categorias";
		$result = DataBase::query($sql);

		return $result;
    }

    public static function existeCategoria($id) {
        $sql = "SELECT id_categoria FROM categorias WHERE id_categoria = :id LIMIT 1";
        $result = DataBase::query($sql, [':id' => $id]);
        return !empty($result);
    }
}