<?php

class ControladorBase{
	private $plantilla;
	private $enventana = false ;
	private $ajax = false ;
	protected $modelo;
	public static $sesion ;

    public function __construct() {
		global $modelo;
        /*
        require_once 'EntidadBase.php';
        require_once 'ModeloBase.php';
        require_once 'paginaBase.php';
        require_once 'ControlArchivo.php';
        */
        // obtengo secion
        // $this::sesion = sesion::constructor() ;

        //Incluir todos los modelos de las bases de datos.
        foreach(glob($modelo->RutaVista("modelo")."*.php") as $file){
			// echo "---cargando:$file\n";
            require_once $file;
        }
        //Incluir todos los modulos auxiliares.

		DebugerCore::msg("en ruta:".$modelo->RutaVista("auxiliar")."*.php");
        foreach(glob($modelo->RutaVista("auxiliar")."*.php",GLOB_NOSORT) as $file){
            DebugerCore::msg("auxiliar: $file");
            require_once $file;
        }
        $this->modelo=$modelo;
        $this->enventana=false;
        $this->plantilla = "index" ; // plantilla por defecto.


    }
    public function logeo(){
		// cuando solicita logeo
		// o puede ser utilizado sin logearse.


	}
    public function iframeError(){

		if ( debugmode ){
			// solo para depuracion.
			if (isset($_GET["ac"])){
				// borrando.
				$fh = date("Y-m-d H:i:s (T)");
				$f=file_put_contents(PATH ."/auxiliar/error.log" ,$fh."--clear data--\n");
			}
			$datos= file_get_contents( PATH ."/auxiliar/error.log");
			// archivo en bruto.
			$tipe="text/html" ;

			header("Content-type: $tipe");
			echo "<!DOCTYPE html>
<html lang=\"es\"><body>
				<a href=\"?ac=clearReg\">limpiar</a><br><a href=\"?ad=recargar\">recargar</a><br>\n";
			echo $datos;
			echo "</doby></html>";
		}
	}

	public function get_sesion($value){
		global $ob_sesion;
		if (isset($ob_sesion)){
			return $ob_sesion->get($value);
		}else{
			$ob_sesion = new sesion();
			return $ob_sesion->get($value);
		}
	}
	
	public function iframe($acion){
		// modalidad de iframe para el html generado
		// url/controlador/modalidad/acion.
		// $this->enventana=true;
		$this->plantilla="";
		// var_dump($acion);
		$this->{$acion[3]}();
	}
	public function ajax($acion){
		// modalidad de iframe para el html generado
		// url/controlador/modalidad/acion.
		$this->plantilla="ajax";
		// var_dump($acion);
		$this->{$acion[3]}();
	}

	public function __call($name, $arguments)
    {
		global $debug;
        // llamada fallida a la clase.
        // todo pasa por aqui.
		$debug->error($name);
		$debug->error($arguments);
		$this->error404();

    }
    public function error404(){
		$name="sin archivo";

		$this->view("404", 	array(
			"name" => $name ,
			"title" => str_replace("Controller","", get_class( $this ) )
		));

     }
	public function set_sesion($name,$value){
		global $ob_sesion;

		if (isset($ob_sesion)){
			return $ob_sesion->set($name,$value);
		}else{
			$ob_sesion = new sesion();
			return $ob_sesion->set($name,$value);
		}
	}

    //Plugins y funcionalidades
     public function salir(){
		 global $ob_sesion;
		 // cerrar session
		// echo "aqui." . __FUNCTION__ ;
		// $od_sesion->borrarsesion();
		$ob_sesion->destroy();

		// session_destroy();
		// unset( $_SESSION );

		// }
	}


/*
* Este método lo que hace es recibir los datos del controlador en forma de array
* los recorre y crea una variable dinámica con el indice asociativo y le da el
* valor que contiene dicha posición del array, luego carga los helpers para las
* vistas y carga la vista que le llega como parámetro. En resumen un método para
* renderizar vistas.
*/

	public function ajaxView($vista,$datos){
		$this->_view($vista,$datos,"ajax");
	}
	private function _view($vista,$datos,$plantilla){
		
		$pagina = new paginaBase($plantilla,$vista,$datos);
		return $pagina->render();
	}
	public function view($vista,$datos,$plantillaTrue=true){
		$plantilla = array( "" , $this->plantilla )[$plantillaTrue ];
		$this->_view($vista,$datos,$plantilla);
	}

    public function plantilla($plantilla){
		$this->plantilla = $plantilla;
		// require_once PATH.'/plantilla/'.$vista.'Plantilla.php';

	}
	public function modelredirect($model,$controlador,$metodo){
		// cambio de modelo
		$this->redirect($controlador,$metodo,"http:",$model);
	}
    public function redirect($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO,$arrayArgumentos=array(),$protocolo='http:',$ruta = URL ){
        // todas las acciones terminan redirigiendo la pagina para cargar origen.
        // por axioma salvaguardar mensaje para la proxima
        // if (isset($ob_sesion->msg)) $ob_sesion->msg
        $archivo=basename($_SERVER['SCRIPT_NAME']);
        require_once 'AyudaVistas.php';

        $tx="";
        foreach($arrayArgumentos as $k=>$v)$tx.="$k=$v&";
		
		if ( $this->enventana ) {
			// redirigir a ventana ( continuar modo iframe.
			$accion="/iframe/".$accion;
		}
		$url=rtrim($ruta,"/");
        header("Location:".$protocolo.$url."/".$archivo."/".$controlador."/".$accion."?".$tx);
		echo "enviando... redirigido a ".$url.$archivo."/$controlador/$accion?$tx";

    }

}
?>
