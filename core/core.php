<?php

/* ************************************************************************

	este es el core principal


	para convertir:
	http://localhost/www/www.iduv.gob.ar/front/index.php/ejemplo/principal?id=12

	PATH_INFO  = 	/ejemplo/principal
	QUERY_STRING  = id=12

	echo $_SERVER["PATH_INFO"];

*****************************************************************************/
	require_once 'error.php';
// ejecutando el autoloader.
// para cargar los controladores automaticamente
	require_once 'Autoloader.php'; Autoloader::register();


	require_once 'ExtensionPuente.php';
	require_once 'ControlArchivo.php';
	require_once 'parametros.php';
	
	// controlador de archivos de objetos.
	require_once 'sesion.php';

	require_once 'documento.php';

	require_once 'objeto.php';

	require_once 'Conectar.php';
	require_once 'ControladorBase.php';
	require_once 'EntidadBase.php';
	require_once 'EntidadBaseFormularios.php';
	require_once 'html.php';

	//require_once 'error.php';
	require_once 'github.php';
	require_once 'htmlinput.class.php';
	require_once 'paginaBase.php';
	require_once 'AyudaVistas.php';

	require_once 'ControladorBase.php';
	//Funciones para el controlador frontal
	require_once 'ControladorFrontal.func.php';

	require_once 'debuger.php';

	DebugerCore::Register();
	DebugerCore::nolog();
	$debug = ChromePhp::getInstance();

	/* acciones apliadas utlizables para depurar.*/
	ChromePhp::log('Hola Consola!');
	ChromePhp::log($_SERVER);
	// ChromePhp::warn('Algo esta mal!!');
	// */


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

	// utilizado unica vez para encontrar la configuracion:
	$premodel = new ControlArchivo($actualDir.DIRECTORY_SEPARATOR.$objetivo.DIRECTORY_SEPARATOR."default");
	$premodel->setAccion("config");
	// echo $premodel->runing("global.php");
	require_once $premodel->runing("global.php");
	unset ($premodel); // descartado de inmediato.
}

$ob_sesion = sesion::getInstance();

if (!function_exists("nz")){
	// funcion general para comprobacion de existencia o inicializacion
	function nz(&$varian,$defaul=""){
		return (isset($varian)?$varian:$defaul);
	}
}
function tiempo($f,$l){
	// global $tiempo ;
	// $tiempo->t( $f , $l);
};
if (isset($_SESSION["login_usuario_activo"])){
	$modelo = new documento($actualDir.DIRECTORY_SEPARATOR.$objetivo.DIRECTORY_SEPARATOR.$_SESSION["login_usuario_Departamento"]);
}else{
	$modelo = new documento($actualDir.DIRECTORY_SEPARATOR.$objetivo.DIRECTORY_SEPARATOR.'default');
}

$paginaGlobal = new objeto(); // objeto general

{
	if (isset($_SERVER["PATH_INFO"])){
		$PathController  = 	explode("/",$_SERVER["PATH_INFO"]) ;
	}else{
		if (isset($modelo->leo) && debugmode ){
			echo shell_exec($modelo->leo );
			exit(0);
		}
		$PathController = array();
	}
};

// var_dump( $PathController);
/* ******************************************++
 *
 * 			funcion principal y accionamiento de
 * todo el front
 *
 * *********************************************/

function core($PathController){
	global $debug;
	//Cargamos controladores y acciones
	// var_dump($PathController);
	/* acciones apliadas utlizables para depurar.*/

// */
		$debug->log('ejecutando accion '.implode("::",$PathController));
		$debug->log($_SERVER);

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
		// en ese momento se lanza todo el programa:

		lanzarAccion($controllerObj,ACCION_DEFECTO);

	}
	// mostrar los errores que se han producido.



}
