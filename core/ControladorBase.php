<?php

class ControladorBase{
	public static $sesion ;
	
    public function __construct() {
		
        require_once 'EntidadBase.php';
        require_once 'ModeloBase.php';
        
        // obtengo secion
        // $this::sesion = sesion::constructor() ;
        
        //Incluir todos los modelos de las bases de datos.
        foreach(glob(PATH."/model/*.php") as $file){
            require_once $file;
        }
        
        //Incluir todos los modulos auxiliares.
        foreach(glob(PATH."/aux/*.php") as $file){
            require_once $file;
        }
        
        
        
    }
    public function iframeError(){
		
		if ( debugmode ){
			if (isset($_GET["ac"])){
				// borrando.
				$fh = date("Y-m-d H:i:s (T)");
				$f=file_put_contents(PATH ."/aux/error.log" ,$fh."--clear data--\n");
			}
			$datos= file_get_contents( PATH ."/aux/error.log");	
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
	
	public function __call($name, $arguments)
    {
        // llamada fallida a la clase.
        // todo pasa por aqui.
        $this->view("404",array("name" => $name , "title" => str_replace("Controller","", get_class( $this ) ) ));
        
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
    public function view($vista,$datos){
        foreach ($datos as $id_assoc => $valor) {
			// echo "<div>$id_assoc = </div>";
			// var_dump($valor);
			// echo "<br>";
			//echo "$valor<br>";
            ${$id_assoc}=$valor; 
        }
        // echo "lanzo:$vista"; 
        require_once 'AyudaVistas.php';
		require_once 'htmlinput.class.php';
        // aqui esta la variable auxiliar de todos los views.
        $helper = new AyudaVistas();
		$imput = new htmlinput();
		
        require_once PATH.'/view/'.$vista.'View.php';
    }
     
    public function redirect($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO,$protocolo='http:'){
        // todas las acciones terminan redirigiendo la pagina para cargar origen.
        // por axioma salvaguardar mensaje para la proxima
        // if (isset($ob_sesion->msg)) $ob_sesion->msg 
        $dt="";
        require_once 'AyudaVistas.php';
        if (debugmode){
			
			$datos = debugf("", 2 );
			if ($datos){
				
				$dt = "&dg=".base64_encode($datos);
				// echo $datos."<<<";
			}
		}
        // header("Location:index.php?ac=".$controlador."&d=".$accion.$dt);
        header("Location:".$protocolo.URL."index.php/".$controlador."/".$accion."?".$dt);
        //echo ("Location:index.php?ac=".$controlador."&d=".$accion.$dt);
    }
     
    //Métodos para los controladores
 
}
?>
