<?php 
Class UsuarioEquipo extends ActiveRecord{

	public function getUsuarioDeEquipo($id_equipo){
		return $this->find("columns: usuario_equipo.usuario_id, usuario.*",
							"conditions: usuario_equipo.activo ='1' and equipo_id = '$id_equipo'",
							"join: inner join usuario on usuario_equipo.usuario_id = usuario.id");
	}
	public function get_usuarios_para_agregar_en_equipo($id)
	{
		$query = "select * from usuario where id not in (select usuario_id from usuario_equipo where activo=1 and equipo_id='$id') and rol >=3";
		return $this->find_all_by_sql($query);
	}
	public function getUsuarioDeMismoEquipo($usuario_id){
		$q="select equipo_id from usuario_equipo where usuario_id = '$usuario_id'";
		$equipo_id = $this->find_all_by_sql($q);

		if (count($equipo_id) == 1) {
			$usuarios = Load::model("usuario_equipo")->find("columns: usuario_id","conditions: equipo_id = '".$equipo_id[0]->equipo_id."'");
			$usuarios_ = array();
			foreach ($usuarios as $key => $value) {
				$usuario__ = Load::model('usuario')->find($value->usuario_id);
				$usuarios_[]="($usuario__->id) ".$usuario__->nombres." ".$usuario__->apellidos;
			}
			$usuarios=$usuarios_;
		}elseif(count($equipo_id) > 1){
			$usuarios = array();
			foreach ($equipo_id as $key => $value) {
				$usuarios_ = Load::model("usuario_equipo")->find("columns: usuario.id as usuario_id, usuario.nombres, usuario.apellidos","conditions: equipo_id = '".$value->equipo_id."'","join: inner join usuario on usuario.id = usuario_equipo.usuario_id");
				foreach ($usuarios_ as $key => $value) {
					
					$usuarios[] = "($value->usuario_id) ".$value->nombres." ".$value->apellidos;
				}
			}
		}elseif(count($equipo_id) == 0){
			return false;
		}


		return array_unique($usuarios); 

	}

}




