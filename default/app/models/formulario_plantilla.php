<?php 
class FormularioPlantilla extends ActiveRecord{
	public function asignacionRepetida($form_id,$plant_id,$equipo_id){
		$registro = $this->find("conditions: formulario_id='$form_id' and plantilla_id='$plant_id' and equipo_id = '$equipo_id'");
		if ($registro) {
			return true;
		}
		return false;
	}
	public function gets($nombre_form = null, $quipo_id = null){
		$where='';
		$q = "SELECT formulario_plantilla.equipo_id, formulario.nombre as nombre_form, plantilla.nombre as nombre_plant, equipo.nombres_abreviado, equipo.pais, equipo.ciudad, formulario_plantilla.formulario_id 
				FROM formulario_plantilla
			inner join formulario on formulario.id = formulario_plantilla.formulario_id 
			inner join plantilla on plantilla.id = formulario_plantilla.plantilla_id
            inner join equipo on equipo.id = formulario_plantilla.equipo_id";
        if ($nombre_form) {
        	$where = "  where formulario.nombre='$nombre_form' and formulario_plantilla.equipo_id = '$quipo_id'";
        }
        // echo $q.$where;
        $r = $this->find_all_by_sql($q.$where);
        return $r;
	}
	public function getAsignacionesByGrupoId($equipo_id){
		$q = "SELECT formulario_plantilla.id as formulario_plantilla_id,formulario.nombre as nombre_form, plantilla.nombre as nombre_plant, plantilla.id as plantilla_id, equipo.id as equipo_id, equipo.nombres_abreviado, equipo.pais, equipo.ciudad, formulario_plantilla.formulario_id 
				FROM formulario_plantilla
			inner join formulario on formulario.id = formulario_plantilla.formulario_id 
			inner join plantilla on plantilla.id = formulario_plantilla.plantilla_id
            inner join equipo on equipo.id = formulario_plantilla.equipo_id
            where formulario_plantilla.equipo_id='$equipo_id'";
            $r = $this->find_all_by_sql($q);
        return $r;    
	}
}


 ?>