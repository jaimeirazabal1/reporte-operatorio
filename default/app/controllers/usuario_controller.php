<?php 
class UsuarioController extends AppController{
	public function crear(){
		$this->titulo = 'Crear Usuarios';
		if (Input::haspost("usuario")) {
			$usuario = Load::model('usuario',Input::post('usuario'));
			$usuario->asignarPasswordUsuario();
			if ($usuario->save()) {
				Flash::valid("Usuario Registrado");
				Input::delete();
			}else{
				Flash::error("No se registro el usuario");
			}
		}
	}
	public function listar(){
		$this->titulo = 'Usuario';
		if (Input::get('rol')) {
			$this->titulo = $this->roles[Input::get('rol')];
			$this->usuarios = Load::model("usuario")->find("conditions: rol='".Input::get('rol')."' and activo='1'");
		}else{
			$this->usuarios = Load::model("usuario")->find();
		}
	}
	public function listareditores(){
		if (Input::get('rol')) {
			$this->titulo = $this->roles[Input::get('rol')];
			$this->usuarios = Load::model("usuario")->find("conditions: rol='".Input::get('rol')."' and activo='1'");
		}else{
			$this->usuarios = Load::model("usuario")->find();
		}
	}
	public function creareditores(){
		if (Input::haspost("usuario")) {
			$usuario = Load::model('usuario',Input::post('usuario'));
			$usuario->rol = Input::get('rol');
			$u=Input::post('usuario');
			if ($u['re_contrasenia']) {
				
				if ($u['re_contrasenia']!=$u['contrasenia']) {
					Flash::error("Las Contraseñas deben coincidir!");
					return;
				}
				$usuario->contrasenia = $usuario->crearPassword($u['contrasenia']);
			}
			if ($usuario->save()) {
				Flash::valid("Editor Registrado");
				Input::delete();
				Router::redirect("usuario/listareditores?rol=".Input::get('rol'));
			}else{
				Flash::error("No se registro el Editor");
			}
		}
	}
	public function editareditores($id_editor){
		if (Input::haspost("usuario")) {
			$usuario = Load::model('usuario',Input::post('usuario'));
			$usuario->rol = Input::get('rol');
			$u=Input::post('usuario');
			if ($u['re_contrasenia']) {
				
				if ($u['re_contrasenia']!=$u['contrasenia']) {
					Flash::error("Las Contraseñas deben coincidir!");
					return;
				}
				$usuario->contrasenia = $usuario->crearPassword($u['contrasenia']);
			}
			if (empty($_POST['usuario']['contrasenia'])) {
				$use = Load::model("usuario")->find($id_editor);
				$usuario->contrasenia = $use->contrasenia;
			}
			if ($usuario->update()) {
				Flash::valid("Editor Editado");
				Input::delete();
				Router::redirect("usuario/listareditores?rol=2");
			}else{
				Flash::error("No se edito el Editor");
			}
		}
		$this->usuario = Load::model("usuario")->find($id_editor);
	}
	public function eliminareditor($id_editor){
		$editor = Load::model('usuario')->find($id_editor);
		if ($editor) {
			$editor->activo = false;
			if ($editor->update()) {
				Flash::valid("Editor Eliminado!");
			}else{
				Flash::error("No se pudo eliminar al editor!");
			}
		}else{
			Flash::warning("El Editor NO existe!");
		}
		Router::redirect("usuario/listareditores?rol=2");

	}
	public function crearadministrador(){
		if (Input::haspost("usuario")) {
			$usuario = Load::model('usuario',Input::post('usuario'));
			$usuario->rol = Input::get('rol');
			$u=Input::post('usuario');
			if ($u['re_contrasenia']) {
				
				if ($u['re_contrasenia']!=$u['contrasenia']) {
					Flash::error("Las Contraseñas deben coincidir!");
					if (Input::hasPost("url_")) {
						$url = Input::post("url_");
						Router::redirect($url);
					}
					return;
				}
				$usuario->contrasenia = $usuario->crearPassword($u['contrasenia']);
			}
			if ($usuario->save()) {
				Flash::valid("Administrador Registrado");
				if (Input::hasPost("url_")) {
					$url = Input::post("url_");
					$equipo_id = explode('/', $url);
					if (Load::model("equipo")->set_admin($equipo_id[2],$usuario->getLastId())) {
						Flash::valid("Administrador ReAsignado");
					}else{
						Flash::error("No se pudo reasignar el administrador");
					}
					Router::redirect($url);
				}
			}else{
				Flash::error("No se registro el Administrador");
			}
		}
		if (Input::hasPost("url_")) {
			$url = Input::post("url_");
			Router::redirect($url);
		}
	}
	public function get_administradores_ajax($id_administrador){
		View::select(null,'json');
		$administrador=Load::model('usuario')->find_first('conditions: activo=1 and rol=3 and id='.$id_administrador);
		$this->data = $administrador;
	
	}
	public function get_administradores(){
		View::select(null,null);
		$administrador=Load::model('usuario')->find('conditions: activo=1 and rol=3');
		$option="<option>Administrador</option>";
		foreach ($administrador as $key => $value) {
			$option.="<option value='".$value->id."'>{$value->nombres} {$value->apellidos}</option>";
		}
		echo $option;		
	}
	public function modificar($id){
		$this->titulo = 'Modificar Usuario';
		if (Input::hasPost('usuario')) {
			$usuario = Load::model('usuario',Input::post('usuario'));
			if ($usuario->update()) {
				Flash::valid("Usuario Modificado");
			}else{
				Flash::error("No se Modifico el usuario");
			}
		}
		$this->usuario = Load::model('usuario')->find($id);
	}
	public function pass($usuario_id = ""){
		if (Auth::get("rol") > 3) {
			if ($usuario and ($usuario != Auth::get("id"))) {
				Flash::error("Acceso denegado");
				Router::redirect("/");
				return;
			}
		}
		if (!$usuario_id) {
			$usuario_id = Auth::get('id');
		}
		$this->titulo = "Cambio de Password";
		if (Input::post("pass")) {
			$usuario = Load::model("usuario")->find($usuario_id);
			$usuario->crearPassword(Input::post('pass'));
			if ($usuario->update()) {
				Flash::valid("Se cambio la contrasenia correctamente");
			}else{
				Flash::error("No se pudo cambiar la contrasenia");
			}
		}
	}
}

 ?>