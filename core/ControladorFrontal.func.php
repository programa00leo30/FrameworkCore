<?php
// parte sesion del sistema. activar entorno global
// require_once 'error.php';
require_once 'sesion.php';
require_once 'objeto.php';

// start_sesion();

//FUNCIONES PARA EL CONTROLADOR FRONTAL
 
function cargarControlador($controller){
	global $_SESSION,$ob_sesion,$error_handle;
	
	//$ob_sesion = new sesion();
	// obtengo el inicio de sesion.
	$ob_sesion = sesion::getInstance();

// 	if (isset($ob_sesion->registrado)){
//		return cargarControladorSeguro($controller);
//	}else
	{
		// login.
		// por defecto. verificar sesion.
		
		$controlador=ucwords(CONTROLADOR_DEFECTO).'Controller';
		$strFileController=PATH.'/controller/'.ucwords(CONTROLADOR_DEFECTO).'Controller.php';  
		
		//if ( file_exists( PATH.'/controller/'.ucwords($controller).'Controller.php' ))
		{	
			$controlador=ucwords($controller).'Controller';
			$strFileController=PATH.'/controller/'.ucwords($controller).'Controller.php' ;
		}
		
		// falla al adquirir controlador.
		if ( file_exists( $strFileController ) ) {
			require_once $strFileController;
		} else {
			// no esta el controlador tomo controlador por defecto.
			$controlador=ucwords(CONTROLADOR_DEFECTO).'Controller';
			$strFileController=PATH.'/controller/'.ucwords(CONTROLADOR_DEFECTO).'Controller.php'; 
			require_once $strFileController;
		}
		
		$controllerObj=new $controlador();
		// $_SESSION = $controllerObj->sesion();
		
		return $controllerObj;
	}
}


function cargarAccion($controllerObj,$action){
    global $_SESION,$ob_sesion ;
    // echo "accion: $action";
    $accion=$action;
    
    $controllerObj->$accion();
    
}
 
function lanzarAccion($controllerObj,$ac){
	global $_SESION,$ob_sesion ;
	
	
	if ($controllerObj == "") {
		cargarAccion($controllerObj, ACCION_DEFECTO);
	}else{
		cargarAccion($controllerObj, $ac);
	}
/*		
	if (isset($_SESSION["fin"])){
		
		cargarAccion($controllerObj, ACCION_DEFECTO);	
	
	}elseif (isset($_GET["d"]) && method_exists($controllerObj, $_GET["d"])){
        cargarAccion($controllerObj, $_GET["d"]);
    }else{
        cargarAccion($controllerObj, ACCION_DEFECTO);
    }
*/
}


function mensaje($mensaje,$render=false){
	global $_SESSION;
	static $msn="";
	static $coun=0;
	
	if (($coun==0) and isset($_SESSION["mensajes"])){
		$msn = $_SESSION["mensajes"];
	}
	$msn.="\t<div >".$mensaje."</div>\n";
	if ($render ){
		if ($coun>0){
			unset($_SESSION["mensajes"]);
			return "\t<h2>".$msn."</h2>\n";
		}else{
			return "";
		}
	}
	$_SESSION["mensajes"] = $msn;
	$coun++;	
	
}
function accesso(){
	global $_SESSION;
	//determina el nivel del cliente.
	if (isset($_SESSION["login"])){
		// cliente logeado.
		$nl=0;
	}elseif (isset($_SESSION["nologin"])){
		// cliente sin privilegios
		$nl=50;
	}else{
		// no se logeo
		$nl=99;
	}
	return $nl;
	
}
function debugf($mensaje,$render=0){ // falso.
	
	static $msn="";
	static $con=0;
	if ($render == true) $render =1;
	/*
	$llamado = debug_backtrace();
		// var_dump($llamado);
		echo "llamado desde:".$llamado[0]["line"].": clase:".$llamado[0]["class"]."<br>\n";
		
	*/
	if ($render == 2){
		// modo especial
		if ($con > 0 )
			$t = "(".$con.")".$msn ;
		else
			$t = false;
		
		return $t;
	}else{	
		$msn.="\t\t<div >($con)".$mensaje."</div>\n";
		$con ++ ;
		if (($render <> 0) and debugmode){
			$a = new AyudaVistas();
			$url = $a->url("login","salir");
			$cerrar = <<<ENC
			<div class="left">
	<a href="$url" class="btn btn-danger">cerrar secion</a>
	</div>
ENC
	;
			return "\t<div>".$msn."</div>\n$cerrar";
		}
	}
}
function vardump($variable){
	// generar un var_dump para alguna variable.
	ob_start();
		var_dump($variable);
		$sal=ob_get_contents() ;
	ob_flush();
	debugf("var_dump::".$sal);
}

function debugmode($tipo=""){
	
	if ($tipo == "not"){
		return true;
		// return !debugmode ;
	}else{
		return debugmode ;
	}

}

/**
* Simple autoloader, so we don't need Composer just for this.
*/
class Autoloader
{
    public static function register()
    {
		
        spl_autoload_register(function ($class) {
			
			
			// $controlador=ucwords($class).'Controller';
			$strFileController=PATH.'/controller/'.ucwords($class).'.php';
		    // $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            
            if (file_exists($strFileController)) {
                require_once $strFileController;
                return true;
            }
            return false;
        });
    }
}

Autoloader::register();
