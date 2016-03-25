<?php 
class IndexController extends AppController{
	public function index() {
		$this->intervenciones_personales = Load::model("equipo")->intervencionesPersonales(Auth::get('id'));
		$this->intervenciones_por_equipo = Load::model('equipo')->intervencionesPorEquipo(Auth::get('id'));
		$this->grupos = Load::model('equipo')->find("columns: equipo.*",
													"join: inner join usuario_equipo on usuario_equipo.equipo_id = equipo.id",
													"conditions: usuario_equipo.usuario_id = '".Auth::get('id')."' ");
	}
	public function perfilpersonal(){
		$this->titulo = "Perfil Personal";
		/*error_reporting(E_ALL);
		ini_set('display_errors', '1');*/
		
	}
}

 ?>