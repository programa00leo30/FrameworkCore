<?php

/* ************************************************************************

	este es el core principal 
	

	para convertir:
	http://localhost/www/www.iduv.gob.ar/front/index.php/ejemplo/principal?id=12
	
	PATH_INFO  = 	/ejemplo/principal 
	QUERY_STRING  = id=12
	
	echo $_SERVER["PATH_INFO"];
	
*****************************************************************************/

/*
 * clase tiempo para saber demoras del script.
 * /

class tiempo{
	private static $iniTimer;
	public function __construct(){
		self::$iniTimer = microtime(true);
	}
	public function __call($name,$arg){
		echo "<div>(".round( microtime(true) - self::$iniTimer ,5).")archivo:".$arg[0]." linea:".$arg[1]."</div>\n";
	}
} ;
$tiempo = new tiempo();
// */

require_once 'error.php';
require_once 'ControlArchivo.php';
// controlador de archivos de objetos.
	require_once 'sesion.php';
	require_once 'objeto.php';


	require_once 'ExtensionPuente.php';
	require_once 'Conectar.php';
	require_once 'ControladorBase.php';
	require_once 'EntidadBase.php';
	require_once 'EntidadBaseFormularios.php';
	//require_once 'error.php';
	require_once 'github.php';
	require_once 'htmlinput.class.php';
	require_once 'objeto.php';
	require_once 'paginaBase.php';
	require_once 'AyudaVistas.php';

	require_once 'ControladorBase.php';
	//Funciones para el controlador frontal
	require_once 'ControladorFrontal.func.php';
// $error_handle = new MiControlError();
if (!defined ("PATH") ){
	/*
	 * no se ha adquirido ningun archivo de configuracion.
	 * 
	 * estructura:
	 * /raiz/sitio/   /camino / modelo / actuador / archivo
	 * |------perfil----------|modelo|actuador| archivo.
	 * 
	 */ 
	
	$premodel = new ControlArchivo($actualDir.$objetivo."/default");
	$premodel->setActuador("config");
	// echo $premodel->runing("global.php");
	require_once $premodel->runing("global.php");
	unset ($premodel); // descartado de inmediato.
	
}

$ob_sesion = sesion::getInstance();
if (!function_exists("nz")){
	// funcion general para comprobacion de existencia o inicializacion
	function nz($varian,$defaul=""){
		return (isset($varian)?$varian:$defaul);
	}
}
function tiempo($f,$l){
	// global $tiempo ;
	// $tiempo->t( $f , $l);
};
if (isset($_SESSION["login_usuario_activo"])){
	$modelo = new ControlArchivo(PATH.'/'.$_SESSION["login_usuario_Departamento"]);
}else{
	$modelo = new ControlArchivo(PATH.'/default');
}

$paginaGlobal = new objeto(); // objeto general


if (isset($_SERVER["PATH_INFO"])){
	$PathController  = 	explode("/",$_SERVER["PATH_INFO"]) ;
}else{
	$PathController = array();
}


// ejecutando el autoloader.
// para cargar los controladores automaticamente.
Autoloader::register();
Debuger::Register();

/* ******************************************++
 * 
 * 			funcion principal y accionamiento de 
 * todo el front
 * 
 * *********************************************/

function core($PathController){
	//Cargamos controladores y acciones
	// var_dump($PathController);
	if(count($PathController) >= 3){
		// cargar el objeto controlador
		
		$controllerObj=cargarControlador($PathController[1]);
		// disparar la accion de ese objeto.
		
		if(isset($PathController[3])){
			// hay mas de tres solicitudes. son mas de una modificiacion.
			lanzarAccion($controllerObj,$PathController[2],$PathController ) ;
		}else { 
			// hay 3 soicitudes, una sola es modificacion.
			lanzarAccion($controllerObj,$PathController[2]);
		}
	}else{
		// no hay modificadores ni control y accion. 
		// se toma por defecto.
		$controllerObj=cargarControlador(CONTROLADOR_DEFECTO);
		lanzarAccion($controllerObj,ACCION_DEFECTO);
	}
	// mostrar los errores que se han producido.
	
	
}

