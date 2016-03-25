<?php 

class PlantillaController extends AppController{
	public function index(){
        $this->titulo='Plantillas';
		$this->plantillas = Load::model("plantilla")->find();
        Load::lib('leer_docx');
        $doc = new LeerDocx();
        echo $doc->read_doc_file(getcwd()."files/upload/plantillas/2016_02_18_05_11_28_Plant_HNDM_ver4.docx");

	}
	public function subir(){

        if (Input::hasPost('oculto')) {  //para saber si se envió el form

            $url = getcwd()."/files/upload/plantillas/";
            $_FILES['archivo']['name'] = date("Y_m_d_H_i_s_").$_FILES['archivo']['name'];
            $archivo = Upload::factory('archivo');//llamamos a la libreria y le pasamos el nombre del campo file del formulario
            $archivo->setExtensions(array('docx')); //le asignamos las extensiones a permitir
            $archivo->setPath($url);
          
            $plantilla = Load::model("plantilla");
            $url_descarga = "/files/upload/plantillas/";
            $result = $plantilla->guardarArchivo(Input::post("nombre"),$url_descarga.$_FILES['archivo']['name']);
            $nombre_doc = explode('.',$_FILES['archivo']['name']);
            if ($archivo->isUploaded() and $result) { 
                if ($archivo->save()) {
                    //$urlToConvert=$url.$_FILES['archivo']['name'];
                    //$result = $this->leerdoc($urlToConvert,$nombre_doc[0]);
                    
                    Flash::valid('Archivo subido correctamente...!!!');
                    Router::toAction("index");
                }else{
            		$plantilla->borrarArchivoPorNombre(Input::post("nombre"));

                	Flash::error("No se subió el archivo correctamente");
                }
            }else{
            	$plantilla->borrarArchivoPorNombre(Input::post("nombre"));
                Flash::warning('No se ha Podido Subir el Archivo...!!!');
            }
        }
       
    
 
	}
    public function asignar(){
        $this->titulo = "Asignar Plantilla";
        $this->plantillas = Load::model("plantilla")->find();
        $this->formularios = Load::model('formulario')->find();
        $this->equipos = Load::model("equipo")->find('conditions: pausa is null');
        if (Input::post("formulario_id") and Input::post("plantilla_id") and Input::post("equipo_id")) {
            $registro = Load::model('formulario_plantilla');
            if (!$registro->asignacionRepetida(Input::post('formulario_id'),Input::post('plantilla_id'),Input::post('equipo_id'))) {
                $registro->formulario_id = Input::post('formulario_id');
                $registro->plantilla_id = Input::post('plantilla_id');
                $registro->equipo_id = Input::post('equipo_id');
                $registro->usuario_id = Auth::get('id');
                if ($registro->save()) {
                    Flash::valid('Asignacion Realizada!');
                }else{
                    FLash::error('No se realizo la asignacion!');
                }
            }else{
                Flash::error("Esta asignacion ya se habia hecho, por favor haga una asignacion diferente!");
            }
        }
    }
    public function editar($id){
        View::select('subir');
        if (Input::hasPost('oculto')) {  //para saber si se envió el form

            $url = getcwd()."/files/upload/plantillas/";
            $_FILES['archivo']['name'] = date("Y_m_d_H_i_s_").$_FILES['archivo']['name'];
            $archivo = Upload::factory('archivo');//llamamos a la libreria y le pasamos el nombre del campo file del formulario
            $archivo->setExtensions(array('docx')); //le asignamos las extensiones a permitir
            $archivo->setPath($url);
            $plantilla = Load::model("plantilla");
            $url_descarga = "/files/upload/plantillas/";
            $refs = $plantilla->getArchivosRef(Input::post('id'));
            $result = $plantilla->guardarArchivo(Input::post("nombre"),$url_descarga.$_FILES['archivo']['name']);
            $nombre_doc = explode('.',$_FILES['archivo']['name']);
            if ($archivo->isUploaded() and $result) { 
                if ($archivo->save()) {
                    /*$urlToConvert=$url.$_FILES['archivo']['name'];
                    $result = $this->leerdoc($urlToConvert,$nombre_doc[0]);
                    echo "<pre>";
                    print_r($refs);
                    die;*/
                    if(!$plantilla->eliminarRefs($refs)){
                        Flash::error("No se eliminaron las referencias anteriores");
                    }
                    Flash::valid('Archivo subido correctamente...!!!');
                    Router::toAction("index");
                }else{
                    Flash::error("No se subió el archivo correctamente");
                }
            }else{
                Flash::warning('No se ha Podido Subir el Archivo...!!!');
            }
        }
        $this->plantilla = Load::model("plantilla")->find($id);
    }
	public function eliminar($id){
        $plantilla = Load::model('plantilla')->find($id);
        if (file_exists(getcwd().$plantilla->url)) {
            # code...
            $rtf = unlink(getcwd().$plantilla->url);
        }else{
            $rtf=true;
        }
        $nombre = explode('.',$plantilla->url);
        $nombre = $nombre[0];
        $nombre = explode('/',$nombre);
        $nombre = $nombre[count($nombre)-1];
        if (file_exists(getcwd().'/files/upload/pdf/'.$nombre.'.pdf')) {
            # code...
        $pdf = unlink(getcwd().'/files/upload/pdf/'.$nombre.'.pdf');
        }else{
            $pdf=true;
        }

		if ($pdf and $rtf and $plantilla->delete()) {
			Flash::valid("Plantilla eliminada!");
		}else{
			Flash::error("No se elimino la plantilla");
		}
        Router::toAction("index");
	}
    public function asignadas(){
        $this->asignaciones = Load::model("formulario_plantilla")->gets();
    }
    public function leerdoc($documento,$nombre)
    {
    //require('fpdf/fpdf.php');
        require_once(APP_PATH.'libs/rtf2html.php');
        require_once(APP_PATH.'libs/aux_pdf.php');
        $reader = new RtfReader();
        $rtf = file_get_contents("$documento"); // or use a string
        $reader->Parse($rtf);

        $formatter = new RtfHtml();
        
        $pdf=new PDF_HTML();
        $pdf->SetFont('Arial','',12);
        $pdf->AddPage();
        $text=$formatter->Format($reader->root);
        if(ini_get('magic_quotes_gpc')=='1')
            $text=stripslashes($text);
        $pdf->WriteHTML($text);
        $r=$pdf->Output(getcwd().'/files/upload/pdf/'.$nombre.'.pdf','F');
        
    }  
    public function getDocName($url){
        $part1=explode(".rtf",$url);
        $part2=explode("/",$part1[0]);
        $name = $part2[count($part2)-1];
        return $name;
    }

    public function editarasignada($grupo_id){
        $this->titulo = "Editar Asignacion";
        if (Input::hasPost('editar')) {
            $edicion = Load::model("formulario_plantilla",Input::post("formulario_plantilla"));
            $edicion->usuario_id = Auth::get("id");
            $post = Input::post("formulario_plantilla");
            $form_id = $post['formulario_id'];
            $plant_id = $post['plantilla_id'];
            $equipo_id = $post['equipo_id'];
            //Flash::valid( "conditions: formulario_id=$form_id and plantilla_id=$plant_id and equipo_id=$equipo_id");
            if (Load::model("formulario_plantilla")->find("conditions: formulario_id=$form_id and plantilla_id=$plant_id and equipo_id=$equipo_id")) {
                Flash::error("La asignacion que se intenta realizar, ya estaba hecha. Intente con otra!");
            }else{
                if ($edicion->update()) {
                    Flash::valid("Edicion Realizada!");
                }else{
                    Flash::error("No se realizo la edicion!");
                }
            }
        }
        if (Input::hasPost('asignar')) {
            $asignacion = Load::model('formulario_plantilla',Input::post("formulario_plantilla"));
            $asignacion->usuario_id = Auth::get('id');
            $post = Input::post("formulario_plantilla");
            $form_id = $post['formulario_id'];
            $plant_id = $post['plantilla_id'];
            $equipo_id = $post['equipo_id'];
            if (Load::model("formulario_plantilla")->find("conditions: formulario_id=$form_id and plantilla_id=$plant_id and equipo_id=$equipo_id")) {
                Flash::error("La asignacion que se intenta realizar, ya estaba hecha. Intente con otra!");
            }else{

                if ($asignacion->save()) {
                    Flash::valid("Asignacion realizada!");
                }else{
                    Flash::error("No se realizo la asignacion!");
                }
            }
        }
        $this->formulario_plantilla = Load::model('formulario_plantilla')->getAsignacionesByGrupoId($grupo_id);
    }
    public function eliminar_asignacion($formulario_plantilla_id,$grupo_id){
        $fp = Load::model('formulario_plantilla')->find($formulario_plantilla_id);
        if ($fp->delete()) {
            Flash::valid('Asignacion Eliminada');
        }else{
            Flash::error("No se elimino la asignacion");
        }
        Router::redirect("plantilla/editarasignada/$grupo_id");
    }
}


 ?>