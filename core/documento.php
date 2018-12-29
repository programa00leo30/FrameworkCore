<?php
/* ***************************************
 * esta clase contiene toda las clases 
 * y herramientas necesarias
 * para el entorno de entrada - salida
 * requerido.
 *****************************************/
 
 class documento extends ExtensionPuente {
	
	private $Secion;
	 
	public function __construct($path) {
		// controla todo lo referenta a los archivos.
		parent::addExt(new ControlArchivo($path),$path);
		// controla todo lo referente a los parametros de entrada.
		parent::addExt(new Parametros());
		
		// parametros guardados localmente.
		parent::addExt(new sesion());
		$ob_sesion = sesion::getInstance();
		
	}
	
 
 
 }

