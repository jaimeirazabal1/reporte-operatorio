<?php 
class Intervenciones extends ActiveRecord{
	public function getRegistrosByEquipoId($equipo_id){
		$r = $this->find();
		if ($r) {
			# code...
			$identificador = $r[0]->identificador;
			$identificadores = array();
			$registros=0;

			foreach ($r as $key => $value) {
				$equipos_pertenece = explode(',',$value->equipos_pertenece);
				

				if (!in_array($identificador, $identificadores) and in_array($equipo_id, $equipos_pertenece)) {
					$identificador=$value->identificador;
					$identificadores[]=$identificador;
					$registros++;
				}else{
					$identificador=$value->identificador;
				}	
			
			}
			return $registros;
		}else{
			return NULL;
		}
	}
	public function getByPath($path){
		$r = $this->find("conditions: url_pdf like '%$path'");
		return isset($r[0]) ? $r[0] : NULL;
	}
	public function usuarioHizoIntervencion($usuario_id,$path){
		$r = $this->getByPath($path);
		return (int)$r->usuario_id == (int)$usuario_id;
	}

	public function usuarioPerteneceEquipoIntervencion($path, $usuario_id){
		$r = $this->getByPath($path);
		/*
		CONDICIONES:
		1.usuario que hizo no tiene equipo.
		2.usuario que entra no tiene equipo.
		*/
		$equipos_usuario_que_hizo = Load::model('usuario_equipo')->find("conditions: usuario_id = '".$r->usuario_id."'");
		$equipos_usuario_ingresa = Load::model('usuario_equipo')->find("conditions: usuario_id = '".$usuario_id."'");
		if (empty($equipos_usuario_que_hizo) or empty($equipos_usuario_ingresa)) {
			return false;
		}
		$array_equipos_usuario_que_hizo=array();
		foreach ($equipos_usuario_que_hizo as $key => $value) {
			$array_equipos_usuario_que_hizo[] = $value->equipo_id;
		}
		$array_equipos_usuario_ingresa=array();

		foreach ($equipos_usuario_ingresa as $key => $value) {
			$array_equipos_usuario_ingresa[] = $value->equipo_id;
		}
		
		$result = array_intersect($array_equipos_usuario_que_hizo, $array_equipos_usuario_ingresa);
		if (empty($result)) {
			return false;
		}else{
			return true;
		}
	}
}


 ?>