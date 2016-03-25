<?php 
class CodigoApi extends ActiveRecord{
	public function restarCredito(){
		$r = $this->find("limit: 1","order: id desc");
		if ($r[0]->credito <= 2) {
			$correos = array('apenha15@gmail.com','jaimeirazabal1@gmail.com');

		// Varios destinatarios
		$para  = 'jaimeirazabal1@gmail.com' . ', '; // atención a la coma
		//$para .= 'wez@example.com';

		// título
		$titulo = 'QUEDAN POCOS CREDITOS PARA CREAR PDFs';

		// mensaje
		$mensaje = 'Es necesario recargar los creditos de pdf. quedan: '.$r[0]->credito.' creditos';

	
		$resultado_mail = mail($para, $titulo, $mensaje);
		
			if(!$resultado_mail){
				Flash::error("Ocurrio un error grave, por favor contacte con el administrador!!! ".__LINE__);
			}
			$r[0]->credito = $r[0]->credito-1;

		}else{

			$r[0]->credito = $r[0]->credito-1;
		}
		$r[0]->update();
	}

	public function quedanCreditos(){
		$r = $this->find("limit: 1","order: id desc");
		if ($r[0]->credito == 0) {
			$correos = array('apenha15@gmail.com','jaimeirazabal1@gmail.com');

			$para      = 'jaimeirazabal1@gmail.com';
			$titulo    = 'Se acabaron los creditos para generar PDF';
			$mensaje   = 'No quedan creditos para crear pdf, los usuarios estan intentando crear registros y no pueden';
	

			if(!mail($para, $titulo, $mensaje)){
				Flash::error("Ocurrio un error grave, por favor contacte con el administrador!!! ".__LINE__);
			}
			return false;
		}else{
			return true;
		}
	}

}


 ?>