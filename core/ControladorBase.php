<?php

class ControladorBase{
	private $plantilla;
	private $enventana = false ;
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
        foreach(glob($modelo->RutaVista()."/model/*.php") as $file){
			// echo "---cargando:$file\n";
            require_once $file;
        }
        //Incluir todos los modulos auxiliares.
        foreach(glob($modelo->RutaVista()."/auxiliar/*.php") as $file){
            require_once $file;
        }
        $this->modelo=$modelo;
        $this->enventana=false;
        $this->plantilla = "index" ; // plantilla por defecto.
        
        
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
		$this->enventana=true;
		// var_dump($acion);
		$this->{$acion[3]}();
	}
	
	public function __call($name, $arguments)
    {
        // llamada fallida a la clase.
        // todo pasa por aqui.
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
    public function view($vista,$datos,$usarPlantilla=TRUE){
		global $paginaGlobal,$modelo;

		if ( $usarPlantilla and !$this->enventana ){
			
			//require_once PATH.'/view/'.$vista.'View.php';
			//$pagina = new paginaBase($plantilla, $vista,$datos);
			
			$pagina = new paginaBase($this->plantilla, $vista,$datos);
			echo $pagina->render();
			
		}elseif ( !$this->enventana ) {
			foreach ($datos as $id_assoc => $valor) {
				// echo "<div>$id_assoc = </div>";
				// var_dump($valor);
				// echo "<br>";
				//echo "$valor<br>";
				${$id_assoc}=$valor; 
				// asignacion de variables globales de entorno.
				$paginaGlobal->$id_assoc=$valor;
			}
			// echo "lanzo:$vista"; 
			
			// aqui esta la variable auxiliar de todos los views.
			$helper = new AyudaVistas();
			$imput = new htmlinput();
			//$f = new ControlArchivo();
			//$f->setActuador("view");
		
			$modelo->setActuador("view");
			// require_once PATH.'/view/'.$vista.'View.php';
			require_once $modelo->runing($vista.'View.php');
		}else{
			// se debe mostrar solo el contenido.
			foreach ($datos as $id_assoc => $valor) {
				// echo "<div>$id_assoc = </div>";
				// var_dump($valor);
				// echo "<br>";
				//echo "$valor<br>";
				${$id_assoc}=$valor; 
			}
			// echo "lanzo:$vista"; 
			
			// $f = new ControlArchivo();
			// $f->setActuador("view");
			$modelo->setActuado("view");
			// aqui esta la variable auxiliar de todos los views.
			$helper = new AyudaVistas();
			$helper->iframe = true; 		// si es en ventana los links se sostienene.
			$html = new htmlinput();
			echo"<!DOCTYPE html>
	<html lang=\"es_AR\"><head></head><body>";
			// require_once PATH.'/view/'.$vista.'ViewContenido.php';
			// require_once PATH.'/view/'.$vista.'ViewContenido.php';
			require_once $modelo->runing($vista.'ViewContenido.php');
			echo "<script>";
			echo "\n". $html->javascript_Render();
			echo "</script>";
			if (debugmode){
				// el signo + viene como un espacio.	
				// echo "<div>".nz($_GET["dg"])."</div>";
				if(isset($_GET["dg"])){
					$dg=$_GET["dg"];
					$msg=base64_decode( str_replace(" ","+",$dg ) );
					echo "<div class='falla'>$msg</div>";
				}
			}
			echo "</body></html>";
		
		}
		
    }
    
    public function plantilla($plantilla){
		$this->plantilla = $plantilla;
		// require_once PATH.'/plantilla/'.$vista.'Plantilla.php';
		
	}
	public function modelredirect($model,$controlador,$accion){
		// cambio de modelo
		$this->redirect($controlador,$accion,"http:",$model);
	}
    public function redirect($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO,$protocolo='http:',$ruta = URL ){
        // todas las acciones terminan redirigiendo la pagina para cargar origen.
        // por axioma salvaguardar mensaje para la proxima
        // if (isset($ob_sesion->msg)) $ob_sesion->msg 
        $dt="";
        require_once 'AyudaVistas.php';
        if (debugmode){	
			// si hay error mostrarlo.
			$datos = debugf("", 2 );
			if ($datos){
				$dt = "&dg=".base64_encode($datos);
				// echo $datos."<<<";
			}
		}
		if ( $this->enventana ) {
			// redirigir a ventana ( continuar modo iframe.
			$accion="/iframe/".$accion;
		}
		$url=rtrim($ruta,"/");
        header("Location:".$protocolo.$url."/".$controlador."/".$accion."?".$dt);
		echo "enviando... redirigido a ".$url."/$controlador/$accion?$dt";
		
    }
 
}
?>
