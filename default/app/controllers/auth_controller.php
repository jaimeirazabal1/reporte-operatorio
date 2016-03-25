<?php 
class AuthController extends AppController{
	public function index(){
		View::template("default");
	}
	public function login(){
        if (Input::hasPost("usuario","contrasenia")){

            $pwd = Load::model('usuario')->crearPassword(Input::post("contrasenia"));
            // die($pwd);
            $usuario=Input::post("usuario");
 
            $auth = new Auth("model", "class: usuario", "usuario: $usuario", "contrasenia: $pwd");
            if ($auth->authenticate()) {
            	Flash::valid("Bienvenid@!");
                Router::redirect("/");
            } else {
                Router::redirect("auth/");
                Flash::warning("Nombre de Usuario o contrasena invalidos");
            }
        }
	}
    public function logout(){
        Auth::destroy_identity();
        Router::redirect('auth/');
    }
}


 ?>