<?php 
class Equipo extends ActiveRecord{
	public function datos_informe($id_informe){
		$query = "SELECT 
					equipo.id as equipo_id,
					equipo.institucion,
					equipo.departamento,
					equipo.especialidades,
					equipo.nombres_abreviado,
					equipo.pais,
					equipo.ciudad,
					equipo.pausa,
					plan.nombre as plan_nombre,
					plan.registros_permitidos,
					usuario.usuario,
					usuario.nombres,
					usuario.apellidos,
					-- usuario.departamento,
					usuario.email,
					usuario.telefono,
					usuario.celular,
					usuario.genero,
					usuario.nacimiento,
					usuario.pais,
					usuario.ciudad,
					usuario.dni,
					usuario.colegiatura,
					usuario.rol
					FROM equipo 
					INNER JOIN plan on equipo.plan_id = plan.id 
					INNER JOIN usuario on equipo.usuario_id = usuario.id 
					";
		$r = $this->find_all_by_sql($query);

		return $r;
	}
	public function set_admin($equipo_id, $usuario_id){
		$equipo = $this->find($equipo_id);
		$equipo->usuario_id = $usuario_id;
		if ($equipo->update()) {
			return true;
		}else{
			return false;
		}
	}
	public function pausarplay($equipo_id)
	{
		$equipo = $this->find($equipo_id);
		if ($equipo->pausa) {
			$equipo->pausa=0;
		}else{
			$equipo->pausa=1;
		}
		if ($equipo->update()) {
			return true;
		}else{
			return false;
		}
	}
	public function obtenerEquiposAsociadosAlUsuario($usuario_id){
		$equipos = Load::model("usuario_equipo")->find("conditions: usuario_id='$usuario_id'");
		return $equipos;
	}
	public function obtenerUsuariosAsociadosDeEquipos($equipos){
		$usuarios = array();
		foreach ($equipos as $key => $value) {
			$_usuarios = Load::model("usuario_equipo")->find("conditions: equipo_id = '".$value->equipo_id."' ");
			foreach ($_usuarios as $usuario_key => $usuario_value) {
				$usuarios[]=$usuario_value->usuario_id;
			}
		}
		return array_unique($usuarios);
	}
	public function getNumeroDeIntervencionesPorEquipo($usuario_id){
		/*
			1-obtener equipos asociados al usuario.
			2-obtener todos los usuarios asociados a los equipos del usuario.
			3-obtener intervenciones de todos los usuarios.
		*/
		/*
			paso 1
		*/
		$equipos = $this->obtenerEquiposAsociadosAlUsuario($usuario_id);
		/*
			paso 2
		*/
		$usuarios = $this->obtenerUsuariosAsociadosDeEquipos($equipos);
		/*
			paso 3
		*/
		$numero_intervenciones=0;
		for ($i=0; $i < count($usuarios) ; $i++) { 
			$intervenciones = Load::model("intervenciones")->find("conditions: usuario_id='".$usuarios[$i]."' ");
			$numero_intervenciones+=count($intervenciones);
		}
		return $numero_intervenciones;
	}
	public function usuarioPerteneceAEquipo($usuario_id,$equipo_id){
		if (Load::model('usuario_equipo')->find("conditions: usuario_id='$usuario_id' and equipo_id='$equipo_id' ")) {
			return true;
		}
		return false;
	}
	public function getNumeroDeIntervencionesByEquipoId($equipo_id){
		$intervenciones = Load::model('intervenciones')->find();
		$identificadores = array();
		$consumo = 0;
		if ($intervenciones) {
			$identificador = $intervenciones[0]->identificador;
			foreach ($intervenciones as $key => $value) {
				$equipos_de_intervencion = explode(',',$value->equipos_pertenece);
				if (!in_array($identificador, $identificadores) and in_array($equipo_id, $equipos_de_intervencion)) {
					$identificadores[] = $identificador;
					$consumo++;

				}
				$identificador = $value->identificador;
			}

			return $consumo;
		}else{
			return false;
		}
	}
	public function getUsuariosByEquipoId($equipo_id){
		return Load::model("usuario_equipo")->find("conditions: usuario_equipo.equipo_id='$equipo_id'",
														"columns: usuario.*",
														"join: inner join usuario on usuario.id = usuario_equipo.usuario_id");

	}
	public function intervencionesPorEquipo($usuario_id){
		$equipos = $this->obtenerEquiposAsociadosAlUsuario($usuario_id);
		$_equipos_id = array();
		$numero_intervenciones=0;
		$suma_tiempo_operatorio='';
		foreach ($equipos as $key => $value) {
			$_equipos_id[]=$value->equipo_id;
		}
		$intervenciones = Load::model("intervenciones")->find("group: equipos_pertenece, identificador");
		$identificadores_usados = array();
		if ($intervenciones) {
			$identificador_ = $intervenciones[0]->identificador;
			foreach ($intervenciones as $key => $value_intervencion) {
				$equipos_ = explode(',',$value_intervencion->equipos_pertenece);
				for ($i=0; $i < count($equipos_) ; $i++) { 
					if ($this->usuarioPerteneceAEquipo($usuario_id,$equipos_[$i]) and !in_array($identificador_, $identificadores_usados)) {
						$numero_intervenciones++;
						if ($suma_tiempo_operatorio != '') {
							$time = unserialize($value_intervencion->formulario);
							$suma_tiempo_operatorio = Util::sumarHoras($suma_tiempo_operatorio,$time['tiempo']);
						}else{
							$suma_tiempo_operatorio = unserialize($value_intervencion->formulario);
							$suma_tiempo_operatorio = $suma_tiempo_operatorio['tiempo'];
	
						}
						$identificadores_usados[]=$identificador_;
					}
				}
				$identificador_= $value_intervencion->identificador;

			}
			// die(var_dump($identificadores_usados));
			//die($suma_tiempo_operatorio);
			return array($numero_intervenciones,$suma_tiempo_operatorio);
		}else{
			return false;
		}
	}
	public function intervencionesPersonales($usuario_id){
		$intervenciones_personales =Load::model('intervenciones')->find("conditions: usuario_id='$usuario_id' ");
		$intervenciones=count($intervenciones_personales);
		$record='';
		foreach ($intervenciones_personales as $key => $value) {
			if ($record != '') {
				$form = unserialize($value->formulario);
				//echo $record," - ",$form['tiempo'],"<br>";
				$record = Util::sumarHoras($record,$form['tiempo']);		
			}else{
				$form = unserialize($value->formulario);
				$record = $form['tiempo'];
			}
		}

		return array($intervenciones,$record);
	}

	public function estaPausadoSuEquipo($usuario_id){
		$r = $this->obtenerEquiposAsociadosAlUsuario($usuario_id);
		foreach ($r as $key => $value) {
			$equipo = $this->find($value->equipo_id);
			

			if ($equipo->pausa) {
				return true;
				break;
			}
		}
		return false;
	}
}

 ?>