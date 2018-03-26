<?php
class indexController extends ControladorBase{
     
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
         
        //Creamos el objeto usuario
        $usuarios = new usuarios();
		
		$this->view("index",array(
            "usuarios"=>$usuarios,
            "Pagtitulo"=>"..::Bienvenido::..",
        ));
    }
	public function salir(){
		// desasociar sesion.
		parent::salir();
		$this->redirect("index","index");
	}
		
}
?>
