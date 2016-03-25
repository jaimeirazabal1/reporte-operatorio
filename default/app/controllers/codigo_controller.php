<?php 
class CodigoController extends AppController{

	public function index(){
		$this->titulo = 'Codigo de Api';
		if (Input::hasPost('codigo_api')) {
			$codigo=Load::model("codigo_api",Input::post("codigo_api"));
			if ($codigo->save()) {
				Flash::valid("Codigo Creado");
				Input::delete();
			}else{
				Flash::error("Codigo no creado");
			}
		}
		$this->codigos= Load::model("codigo_api")->find();
	}
}



 ?>