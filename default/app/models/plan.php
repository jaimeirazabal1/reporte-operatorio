<?php 
class Plan extends ActiveRecord{
	protected function initialize(){
    	$this->validates_uniqueness_of("nombre");
   	}
   	public function getNombrePlanById($id){
   		$plan = $this->find($id);
   		return ucfirst($plan->nombre);
   	}
   	public function getMontoByPlanId($plan_id){
   		$r = $this->find($plan_id);
   		return $r->registros_permitidos;
   	}
   	public function getRegistrosPermitidos(){

   	}
}

 ?>