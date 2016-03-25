<?php 
class IntervencionesController extends AppController{
	public function index(){
		$this->titulo = 'Lista de Intervenciones';

	}
	public function lista(){
		$rol = Load::model("usuario")->getRol();
		$this->titulo =  "Lista de Intervenciones";
		if ((int)$rol == 1  or (int)$rol == 2) {
			$this->intervenciones = Load::model("intervenciones")->find("join: inner join usuario on usuario.id = intervenciones.usuario_id",
																		"columns: intervenciones.*, usuario.usuario");
		}else{
			/*
			OBTENER INTERVENCIONES DE EQUIPO
			Intervenciones::usuarioPerteneceEquipoIntervencion($path,$usuario_id)
			*/
			$intervenciones = Load::model("intervenciones")->find();
			$intervenciones_=array();
			foreach ($intervenciones as $key => $value) {
				/*nombre del documento de la intervencion*/
				$nombre= Util::verNombreArchivoPdf($value->url_pdf);
				/*son las intervenciones que le pertenecen a su equipo*/
				if ($nombre) {
					if (Auth::get('id') == $value->usuario_id) {
						$intervenciones_[] = $value;
					}else{

						if (Load::model('intervenciones')->usuarioPerteneceEquipoIntervencion($nombre, Auth::get('id'))) {
							$intervenciones_[] = $value;
						}
					}
				}
			}
			$this->intervenciones=$intervenciones_;
		}
	}
	public function nuevo(){
		$this->titulo = 'Agregar Intervencion';
		$q = "SELECT 
				formulario.nombre as nombre_formulario,
				formulario.id as id_formulario
			  FROM 
			  formulario_plantilla 
			  INNER JOIN plantilla on formulario_plantilla.plantilla_id = plantilla.id
			  INNER JOIN formulario on formulario_plantilla.formulario_id = formulario.id
			  INNER JOIN usuario_equipo on usuario_equipo.usuario_id = '".Auth::get('id')."'
			  INNER JOIN equipo on formulario_plantilla.equipo_id = usuario_equipo.equipo_id
			  where usuario_equipo.activo ='1' 
			  group by nombre_formulario";

		$this->formularios_disponibles = Load::model('formulario_plantilla')->find_all_by_sql($q);
	}
 	public function crear($formulario_id){
 		$this->titulo = "Crear Reporte";
 		$formulario = Load::model('formulario')->find($formulario_id);
 		$this->campos = unserialize($formulario->campos);
 		$this->nombre = $formulario->nombre;
 		$this->formulario = $formulario_id;
 		if (Input::post("adherir")) {
 			$nombre = Input::post("adherir");
 			$nombre = str_replace("Generar reporte con: ", "", $nombre);
 			$nombre = str_replace(" ", "_", $nombre);
 			
 			$url = $_POST[$nombre];
 			$identificador = md5(date('Y-m-d_H:i:s'));
 			$_POST['identificador'] = $identificador;
 			//Util::p($_POST);die;
 			/*
 				TODO
				1-verificar si el equipo esta pausado,
				2-verificar si tiene creditos,
				3-verificar si la api tiene creditos, mandar un correo a americo (listo)

 			*/
			$api = Load::model('codigo_api');
			if (!Load::model('equipo')->estaPausadoSuEquipo(Auth::get('id'))) {
					
				if ($api->quedanCreditos()) {

		 			foreach ($_POST['fields'] as $key => $value) {
		 				for ($i=0; $i < count($this->campos_autocomplete_usuarios_mismo_equipo) ; $i++) { 
		 					
		 					if (strpos($key, $this->campos_autocomplete_usuarios_mismo_equipo[$i]) !== false) {
		 						if (!empty($value)) {
									$pos_ini = strpos($value,'(');
			 						$pos_fin = strpos($value,')');
			 						$id = substr($value,$pos_ini+1,$pos_fin-1);
			 						$equipos = Load::model('equipo')->obtenerEquiposAsociadosAlUsuario($id);
			 						$equipos_= array();
			 						foreach ($equipos as $key_equipos => $value_equipos) {
			 							$equipos_[]=$value_equipos->equipo_id;
			 						}
			 						if ($id and $id != 0) {
				 						$Intervencion = array(
				 							'id'=>isset($_POST['fields']['id']) ? $_POST['fields']['id'] : NULL,
				 							'usuario_id' => $id,
				 							'formulario' => serialize($_POST['fields']),
				 							'identificador' => $identificador,
				 							'equipos_pertenece'=> implode(",",$equipos_),
				 							'plantilla_nombre' => Input::post("adherir"),
				 							'formulario_id' => Input::post('formulario'),
				 							'creado' => $_POST['fields']['creado']
				 						);
				 						
				 						//$nombres_apellidos = Load::model("usuario")->getNombresYApellidosById($id);

				 						$inter = Load::model("intervenciones",$Intervencion);
				 						if (!$inter->save()) {
				 							
				 							Flash::error("Ocurrio un error sumando la intervencion al usuario ".$nombres_apellidos);
				 						}else{
				 							$this->id = $inter->lastId();
				 							/*$to = "jaimeirazabal1@gmail.com";
				 							$suject = "Prueba cuando se crea reporte";
				 							$message = "Esto es una prueba para cuando se crean reportes";
				 							$to, $nombreUsuario, $departamento, $institucion, $usuario_registro, $enlace_a_pdf
				 							$mail = Mail::avisoNuevoReporte2($to,"Nombre de usuario","Departamento tal", "Institucion tal"," Usuario registro tal","Enlace tal");*/
				 							
				 						}
			 						}
		 						}
		 					}
		 				}
		 				
		 			}
		 			
		 			// foreach ($_POST['fields'] as $key => $value) {
		 			
		 			include_once(APP_PATH.'libs/opentbs/demo/demo_ms_word.php');

		 			$api->restarCredito();
		 			// }
					//Load::lib('opentbs/demo/demo_ms_word');
				}else{
					Flash::error("Contacte con el administrador del sistema, es imposible generar reportes ahora Error:".__LINE__);
				}
			}else{
				Flash::error("Tu equipo esta pausado, no puedes imprimir reportes");
			}
 		}
 	}
}


 ?>