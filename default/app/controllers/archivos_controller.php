<?php

class ArchivosController extends AppController{
	public function get_($path){
		if (!Auth::is_valid()) {
			/*
			CONDICIONES PARA ENTRAR:
			1.Que el usuario que esta entrando sea el que hizo la intervencion.
			2.Que el usuario que esta entrando pertenezca al equipo de la persona que lo escribio.
			3.Que el usuario que esta entrando es administrador general o editor.
			*/
		   	if (Input::hasPost("usuario","password")){

	            $pwd 		= 	Load::model('usuario')->crearPassword(Input::post("password"));
	            $usuario 	=	Input::post("usuario");
	            
	            $usuario_objeto = Load::model('usuario')->getUsuarioByUsuario($usuario);

	            $resultado_uno = Load::model("intervenciones")->usuarioHizoIntervencion($usuario_objeto->id, $path);
	            if (!$resultado_uno) {
	            	$mensajes[] = "El usuario que intenta ingresar no hizo la intervencion";
	            }
	            $resultado_dos = Load::model("intervenciones")->usuarioPerteneceEquipoIntervencion($path, $usuario_objeto->id);
	            if (!$resultado_dos) {
	            	$mensajes[] = "El usuario que intenta ingresar no pertenece al equipo del usuario que hizo la intervencion";
	            }
	            if ($usuario_objeto->rol != 1 and $usuario_objeto->rol != 2) {
	            	$resultado_tres = false;
	            }else{
	            	$resultado_tres = true;
	            }
	            if (!$resultado_tres) {
	            	$mensajes[] = "El usuario que intenta ingresar no posee los privilegios para ingresar";
	            }
	            if (!$resultado_uno and !$resultado_dos and !$resultado_tres) {
	            	$mensaje = "Si no pudo ingresar puede ser por lo siguiente:";
	            	Flash::error($mensaje."<br> -".implode("<br>-",$mensajes));
	            	return;
	            }
	            $auth = new Auth("model", "class: usuario", "usuario: $usuario", "contrasenia: $pwd");
	            if ($auth->authenticate()) {
	          
	                Router::toAction("get_/".Input::post("path"));
	            } else {
	                Flash::warning("Nombre de Usuario o contrasena invalidos");
	            }
	        }
			$this->path = $path;
		}else
		{

			View::select(null,null);
			$file = getcwd()."/files/upload/pdf/".$path;
			//die($file);
			header('Content-type: application/pdf');
			readfile($file);
			die;
		}
	}
	public function get_plantilla($path){
		if (!Auth::is_valid()) {
			Router::redirect("auth/");
		}
		View::select(null,null);
		$file = getcwd()."/files/upload/plantillas/".$path;
		header("Content-disposition: attachment; filename=$file");
		header("Content-type: application/octet-stream");
		readfile($file);
		die;		
	}
}