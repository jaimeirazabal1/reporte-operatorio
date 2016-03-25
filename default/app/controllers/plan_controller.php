<?php 

class PlanController extends AppController{
	public function index(){
		$this->planes = Load::model('plan')->find('conditions: activo=1');
	}
	public function byequipo(){
		$this->titulo='Planes Contratados';
		$this->planes_by_equipo= Load::model('plan_equipo')->find("join: inner join plan on plan.id = plan_equipo.plan_id inner join equipo on equipo.id = plan_equipo.equipo_id","columns: plan.id as plan_id, plan.nombre as nombre_plan, plan.registros_permitidos, equipo.*","order: plan_equipo.id asc");

	}
	public function crear(){
		if (Input::haspost("plan")) {
			$plan = Load::model("plan",Input::post("plan"));
			if ($plan->save()) {
				Flash::valid("El plan se registro con exito!");
				Router::redirect("plan/");
			}else{
				Flash::error("No se creo el plan!");
			}
		}
	}
	public function editar($id_plan){
		if (Input::haspost("plan")) {
			$plan = Load::model('plan',Input::post("plan"));
			if ($plan->update()) {
				Flash::valid("Plan Editado!");
				Router::redirect("plan/");
			}else{
				Flash::error("No se edito el plan!");
			}
		}
		$this->plan = Load::model('plan')->find($id_plan);
	}
	public function eliminar($id_plan){
		$plan = Load::model("plan")->find($id_plan);
		if ($plan) {
			$plan->activo = false;
			if ($plan->update()) {
				Flash::valid("Plan Eliminado");
			}else{
				Flash::error("No se elimino el plan");
			}
		}else{
			Flash::warning("No se encontro el plan!");
		}
		Router::redirect("plan/");
	}
}

 ?>