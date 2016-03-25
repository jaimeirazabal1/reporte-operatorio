<?php 
class UsuarioEquipoController extends AppController{
	public function agregar($id_equipo){
		$this->titulo = 'Administrador de Equipo';
		$this->usuario = Load::model('usuario')->find("conditions: rol >= 3");
		$this->usuarios_equipo = Load::model("usuario_equipo")->getUsuarioDeEquipo($id_equipo);
		$usuarios = Load::model("usuario_equipo")->get_usuarios_para_agregar_en_equipo($id_equipo);
		if ($usuarios) {
			# code...
			$html = "<div class='container'>";
			$html.=' <ul class="list-group" style="width:500px !important">';
			foreach ($usuarios as $key => $value) {
				$html.='<li class="list-group-item">';
				$html.="<div class='check'>";
				$html.="<label><input type='checkbox' class='usuarios_agregando' id='".$id_equipo."' value='".$value->id."' name='usuario_id[]'>&nbsp;&nbsp;".$value->nombres." ".$value->apellidos." <br> Aparicion: ".$value->aparicion." <br> Email: ".$value->email."</label>";
				$html.="</div>";
				$html.='</li>';

			}
			$html.='</ul>';
			$html.='</div>';
			$this->html=$html;
		}else{
			$this->html="<h3>No hay usuarios para agregar en el Equipo</h3>";
			
		}
	}
	public function get_usuarios_not_equipo($equipo_id)
	{
		View::select(null,'json');
		foreach ($_GET as $key => $value) {
			if ($key != '_url' and $key != 'valor0') {
				$usuario_equipo = Load::model("usuario_equipo");
				$usuario_equipo->equipo_id = $equipo_id;
				$usuario_equipo->usuario_id = $value;
				$usuario_equipo->activo = 1;
				if(!$usuario_equipo->save()){
					$error[]='El usuario id: '.$value.' no se pudo registrar en el equipo id: '.$equipo_id;
				}
			}
		}

		if (isset($error)) {
			$this->data = array('response'=>false,'errores'=>implode('\n <br>', $error));
		}else{
			$this->data = array('response'=>true);
		}

	}
	// public function get_usuarios_equipo($equipo_id){
	// 	View::select(null,null);
	// 	$usuarios = Load::model("usuario_equipo")->getUsuarioDeEquipo($equipo_id);
	// 	$html = "<div class='container'>";
	// 	$html.=' <ul class="list-group" style="width:500px !important">';
	// 	foreach ($usuarios as $key => $value) {
	// 		$html.='<li class="list-group-item">';
	// 		$html.="<div class='check'>";
	// 		$html.="<label><input type='checkbox' class='usuarios_agregando' id='".$equipo_id."' value='".$value->id."' name='usuario_id[]'>&nbsp;&nbsp;".$value->nombres." ".$value->apellidos." <br> Aparicion: ".$value->aparicion." <br> Email: ".$value->email."</label>";
	// 		$html.="</div>";
	// 		$html.='</li>';

	// 	}
	// 	$html.='</ul>';
	// 	$html.='</div>';
	// 	echo $html;
	// }

	public function cambiar_admin($equipo_id)
	{
		if (Input::hasPost('usuario_id')) {
			$usuario_id = Input::post('usuario_id');
			if (Load::model("equipo")->set_admin($equipo_id,$usuario_id)) {
				Flash::valid("El Administrador Fue cambiado");
			}else{
				Flash::error("No se pudo cambiar al administrador");
			}
		}
		Router::redirect('usuario_equipo/agregar/'.$equipo_id);
	}

}
?>