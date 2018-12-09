<?php

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
			// $strFileController=PATH.'/controller/'.ucwords($class).'.php';

			$modelo->setAccion('controller');
            if ( $modelo->Existe(ucwords($class).'.php')){
				require_once $modelo->runing(ucwords($class).'.php');
				return true;
				// if (file_exists($strFileController)) {
				//    require_once $strFileController;
				//    return true;
				//}
			}else{
				// probando si es un modelo.:
				$modelo->setAccion('model');
				if ( $modelo->Existe($class.'.php')){
					require_once $modelo->runing($class.'.php');
					return true;
				}
			}
            return false;
        });
    }
}
