<?php

// start_sesion();

//FUNCIONES PARA EL CONTROLADOR FRONTAL
 
function cargarControladorSeguro($controller){

	global $_SESSION,$ob_sesion,$error_handle,$modelo ;	

		$controlador=ucwords($controller).'Controller';
		$modelo->setActuador("controller");
		$strFileController=$modelo->runing( ucwords(CONTROLADOR_DEFECTO).'Controller.php'); 
		  
		$rt=require_once($modelo->runing( ucwords($controller).'Controller.php' ));
		if (!$rt){
			// no existe el controlador, cargando el que es por defecto.
			$strFileController=ucwords(CONTROLADOR_DEFECTO).'Controller.php';
			$rt=require_once($modelo->runing( ucwords(CONTROLADOR_DEFECTO).'Controller.php' ));
			$controlador=ucwords(CONTROLADOR_DEFECTO).'Controller';
		
		}
		if (class_exists ( $controlador )){ // la clase existe.
			$controllerObj=new $controlador();
		}else{ // la clase no existe. cargando controlador por defecto.
			$controlador = (ucwords(CONTROLADOR_DEFECTO).'Controller');
			$controllerObj=new $controlador();
		}	
		
		return $controllerObj;
	}
function cargarControlador($controller){
	global $_SESSION,$ob_sesion,$error_handle,$modelo;
	
	if ( defined( "LOGIN") ){
		// en caso de que se necesite login.
		if ( ! isset( $ob_sesion->login_usuario_activo )){			
			// $controlador=ucwords( LOGIN_controler .'Controller.php');
			return cargarControladorSeguro(LOGIN_controler) ;
		}	
		else
		{
			// cargar controlador despues de que se logeo
			return cargarControladorSeguro($controller) ;
		}
	}
	else
	{
		// cargar controlador sin logeo.
		return cargarControladorSeguro($controller) ;
	}
}



function cargarAccion($controllerObj,$action,$activacion=null){
    global $_SESION,$ob_sesion,$modelo ;
    // echo "accion: $action";
    $accion=$action;
    $controllerObj->$accion($activacion);
    
}
 
function lanzarAccion($controllerObj,$ac,$activacion=null){
	global $_SESION,$ob_sesion ;
	
	
	if ($controllerObj == "") {
		cargarAccion($controllerObj, ACCION_DEFECTO);
	}else{
		cargarAccion($controllerObj, $ac,$activacion);
	}

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
	// if ($render == true) $render =1;
	/*
	$llamado = debug_backtrace();
		// var_dump($llamado);
		echo "llamado desde:".$llamado[0]["line"].": clase:".$llamado[0]["class"]."<br>\n";
		
	*/
	if ($render == 2){
		// modo especial
		if ($con > 0 )
			$t = "(".$con.")".$msn."<br>\n" ;
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

/**
* Autocargador simple, entonces, no tenemos que componer nada. solo esto.
*/
class Autoloader
{
    public static function register()
    {
		
        spl_autoload_register(function ($class) {
			global $modelo;
			// $controlador=ucwords($class).'Controller';
			$strFileController=PATH.'/controller/'.ucwords($class).'.php';
            $modelo->setActuador('controller');
            if ( $modelo->Exite(ucwords($class).'.php')){
				require_once $modelo->runing(ucwords($class).'.php');
				return true;
				// if (file_exists($strFileController)) {
				//    require_once $strFileController;
				//    return true;
				//}
			}
            return false;
        });
    }
}
