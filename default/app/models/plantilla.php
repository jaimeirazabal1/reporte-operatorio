<?php 
class Plantilla extends ActiveRecord{
	public function guardarArchivo($nombre,$url){
		$this->nombre = $nombre;
		$this->url = $url;
		$this->usuario_id = Auth::get("id");
		if (Input::post('id')) {
			$this->id = Input::post('id');
			return $this->update();
		}else{

			return $this->save();
		}
	}
	public function borrarArchivosAnteriores($id){
		$plantilla=$this->find($id);
        $rtf = unlink(getcwd().$plantilla->url);
        $nombre = explode('.',$plantilla->url);
        $nombre = $nombre[0];
        $nombre = explode('/',$nombre);
        $nombre = $nombre[count($nombre)-1];
        $pdf = unlink(getcwd().'/files/upload/pdf/'.$nombre.'.pdf');
        if ($pdf and $rtf) {
        	return true;
        }else{
        	return false;
        }
	}
	public function borrarArchivoPorNombre($nombre){
		$archivo = $this->find_first("conditions: nombre='$nombre'");
		return $archivo->delete();
	}
	public function getArchivosRef($id){
		$plantilla=$this->find($id);
        $pdf = getcwd().$plantilla->url;
        
        return array('pdf'=>$pdf);
	}
	public function eliminarRefs($archivos){
		//Util::p($archivos);die;
		$pdf = unlink($archivos['pdf']);
		//$rtf = unlink($archivos['rtf']);
		
		if ($pdf) {
			return true;
		}else{
			return false;
		}
	}
	public function leef($fichero){
		$texto= file($fichero);
		$tamleef = sizeof($texto);
		$todo='';
		for ($n=0; $n<$tamleef;$n++)
			{ $todo = $todo.$texto[$n];}
		return $todo;
	}
	public function getPlantillas(){
		$plantillas = Load::model("plantilla")->find();
		return $plantillas;
	}
	public function getCamposDePlantillas(){
		$plantillas = $this->getPlantillas();
		$campos = array();
		foreach ($plantillas as $key => $value) {
			if (file_exists(getcwd().$value->url)) {
				$contenido_plantilla = $this->getContenidoPdf(getcwd().$value->url);
				preg_match_all("/[#*]+[A-Za-z0-9]+[#*]/",$contenido_plantilla, $salida, PREG_PATTERN_ORDER);
				$campos = array_merge($campos, $salida[0]);
			}
		}
		return array_unique($campos,SORT_STRING);
	}
	public function getContenidoPdf($url){
		Load::lib('pdf_edit');	
		// Instancia de PDF_EDIT para modificar el archivo
		$parser = new PDF_EDIT(file_get_contents($url));
		echo "<pre>";
		var_dump($parser->getParsedData());
		die;
	}
	public function getPlantillasDeUsuario($usuario_id){
		$q = "select plantilla.*
				from
				plantilla
				inner join formulario_plantilla on formulario_plantilla.plantilla_id = plantilla.id
				inner join usuario_equipo on usuario_equipo.equipo_id = formulario_plantilla.equipo_id
				where usuario_equipo.usuario_id = $usuario_id group by nombre";
		
		return $this->find_all_by_sql($q);
	}
	public function pasarAPdf($docx,$name){

		Load::lib('docxToPdf');
		 
		$myDoc2Pdf = new Doc2PdfConverter();
		$serial=Load::model('codigo_api')->find('limit: 1','order: creado desc');
		$myDoc2Pdf->apiKey = isset($serial[0]->codigo) ? $serial[0]->codigo : "xjodnc2n1t2wdg";
		$myDoc2Pdf->inputDocLocation = $docx;
		//please make sure that the dir is writable chmod 777
		$url_bonito="/".$name.'.pdf';
		$url_salida = 'files/upload/pdf/'.$name.'.pdf';
		$myDoc2Pdf->outputPdfLocation = $url_salida;
		
		$myDoc2Pdf->pdfAuthor ="Jaime Irazabal jaimeirazabal1@gmail.com";
		$myDoc2Pdf->pdfTitle = "Reporte";
		$myDoc2Pdf->pdfSubject = "Reporte";
		$myDoc2Pdf->pdfKeywords ="Reporte";
		//$result will be OK or APINOK or Error Message 
		$result = $myDoc2Pdf->Doc2PdfConvert();

		if ($result=='OK') {
			return $url_bonito;
		}else{
			Flash::valid("Error convirtiendo el documento de docx a pdf");
			die($result);
		}
	}
}


 ?>