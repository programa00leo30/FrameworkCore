<?php

/* 

	este es el core principal 
	
*/
/*

	para convertir:
	http://localhost/www/www.iduv.gob.ar/front/index.php/ejemplo/principal?id=12
	
	PATH_INFO  = 	/ejemplo/principal 
	QUERY_STRING  = id=12
	
	echo $_SERVER["PATH_INFO"];
	
*/
	
	require_once 'error.php';
	// $error_handle = new MiControlError();

if (defined ("PATH")) {
	// PATH_INFO // 
	//Base para los controladores
	
	require_once 'ControladorBase.php';
	 
	//Funciones para el controlador frontal
	require_once 'ControladorFrontal.func.php';

}
else {
	echo "falla critica!";
}

if (isset($_SERVER["PATH_INFO"])){
	$PathController  = 	explode("/",$_SERVER["PATH_INFO"]) ;
}else
	$PathController = array();


