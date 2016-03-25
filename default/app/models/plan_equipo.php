<?php 
class PlanEquipo extends ActiveRecord{

	public function getMontosByEquipoId($equipo_id){
		$r = $this->find("conditions: equipo_id='$equipo_id'");
		// echo "<pre>";
		// print_r(json_decode(json_encode($r)));
		// die;
		$monto = 0;
		foreach ($r as $key => $value) {
			$monto+=$value->monto;
		}
		return $monto;
	}


}




 ?>