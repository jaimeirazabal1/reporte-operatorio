<?php 
class EquipoController extends AppController{
	public function index(){
		$this->titulo = 'Equipos';
		$this->equipos = Load::model("equipo")->find();
	}
	public function crear(){
		$this->titulo = 'Crear Equipo';

		if (Input::haspost('equipo')) {
			$equipo = Load::model("equipo",Input::post("equipo"));
			if (!$equipo->usuario_id) {
				Flash::error("No se selecciono un administrador valido");
				Router::redirect("equipo/crear");
				return;

			}
			if ($equipo->save()) {
				Flash::valid("Equipo Agregado");
				$plan_equipo = Load::model('plan_equipo');
				$plan_equipo->plan_id=$equipo->plan_id;
				$plan_equipo->equipo_id=$equipo->lastId();
				$plan_equipo->monto = Load::model('plan')->getMontoByPlanId($equipo->plan_id);

				if (!$plan_equipo->save()) {
					Flash::error("No se pudo registrar el plan para el equipo, por favor, contactar con el administrador!");
				}
				Router::redirect("equipo/");
			}else{
				Flash::error("No se puedo Agregar el equipo");
			}
		}
	}
	public function editar($id_equipo){
		$this->titulo = 'Crear Equipo';
		if (Input::haspost('equipo')) {
			$equipo = Load::model("equipo",Input::post("equipo"));
			if ($equipo->save()) {
				Flash::valid("Equipo Editado");
		Router::redirect("equipo/");
			}else{
				Flash::error("No se puedo editar el equipo");
			}
		}		
		$this->equipo = Load::model("equipo")->find($id_equipo);
	}
	public function eliminar($id_equipo){
		$equipo = Load::model('equipo')->find($id_equipo);
		if ($equipo and $equipo->delete()) {
			Flash::valid("Equipo Eliminado con exito");
		}else{
			Flash::error("No se pudo eliminar el equipo");
		}
		Router::redirect("equipo/");
	}
	public function reset($id_equipo){
		Flash::valid("Equipo Reseteado! (por desarrollar)");
		Router::redirect("equipo/");
	}
	public function pausarplay($id_equipo){
		$equipo = Load::model('equipo')->find($id_equipo);
		if ($equipo->pausarplay($id_equipo)) {
			if ($equipo->pausa) {
				Flash::valid('El equipo fue puesto en play');
			}else{

				Flash::valid('El equipo fue puesto en pausa');
			}
		}else{
			Flash::error("Error cambiando estado del equipo");
		}
		Router::redirect('equipo/');
	}
	public function informe($id_equipo){
		$this->titulo = 'Informe de Equipo';
		$this->informe_equipo = Load::model('equipo')->datos_informe($id_equipo);
		$this->usuarios_de_equipo = Load::model("equipo")->getUsuariosByEquipoId($id_equipo);
		$this->planes = Load::model('plan_equipo')->find("conditions: equipo_id='$id_equipo'",
														"columns: plan.*",
														"join: inner join plan on plan.id = plan_equipo.plan_id");
		$this->registros = Load::model("intervenciones")->getRegistrosByEquipoId($id_equipo);
	}
	public function usuarios(){
		$this->titulo = "Usuarios por Equipo";
		$this->equipos = Load::model("equipo")->find();

	}
	public function agregar_plan($equipo_id){
		$this->titulo='Agregar Plan';
		if (Input::post('plan_equipo')) {
			$plan_equipo = Load::model("plan_equipo",Input::post('plan_equipo'));
			$plan = Load::model('plan')->find($plan_equipo->plan_id);
			$plan_equipo->monto=$plan->registros_permitidos;
			if ($plan_equipo->save()) {
				Flash::valid("Plan agregado");
				Router::redirect('equipo/');
			}else{
				Flash::error("El plan no se pudo agregar");
			}
		}

		$this->plan_equipo = Load::model("plan_equipo");
		$this->plan_equipo->equipo_id = $equipo_id;
	}
}


 ?>