<?php 
class FormularioController extends AppController{
	public function crear(){
		$this->titulo = "Crear Formulario";
		//$this->campos = Load::model("plantilla")->getCamposDePlantillas();
		if (Input::hasPost("nombre")) {
			$formulario = Load::model("formulario");
			$formulario->nombre = Input::post("nombre");
			if (isset($_POST['label'])) {
				$campos = array();
				$num=0;
				foreach ($_POST['label'] as $key => $value) {
					$campos[$num]['label']=$_POST['label'][$num][0];
					$campos[$num]['campo']=$_POST['campo'][$num][0];
					$campos[$num]['tipo']=$_POST['tipo'][$num][0];
					if (isset($_POST['valores'][$num])) {
						$campos[$num]['valores']=$_POST['valores'][$num][0];
					}

					$num++;
				}
			}
			$formulario->campos = serialize($campos);
			if ($formulario->save()) {
				Flash::valid('Formulario creado');
			}else{
				Flash::error("No se pudo crear el formulario");
			}

		}
	}
	public function index(){
		$this->titulo = "Lista de Formularios";
		$this->formularios = Load::model('formulario')->find();
	}
	public function eliminar($id){
		$form = Load::model('formulario')->find($id);
		if ($form->delete()) {
			Flash::valid("Formulario eliminado");
		}else{
			Flash::error("NO se elimino el formulario");
		}
		Router::redirect('formulario/');
	}
}


 ?>