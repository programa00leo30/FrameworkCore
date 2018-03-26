<?php

// set_error_handler(array('MiControlError', 'errorHandler'));
// cambiando control de errores:
set_error_handler(array('MiControlError', 'gestorErrores'));
// error_reporting(E_ALL | E_STRICT);

if (defined ("debugmode")) {
	// modalidad de depuracion.
	error_reporting  (E_ALL);
	ini_set ('display_errors', true);
}

class MiControlError
{
    protected static $_toStringException;
    protected static $_todoElTexto;
    protected static $contador;

    public static function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
    {
		$txe= "<div>(".$errorNumber.")ERROR:".$errorMessage." en archivo:".$errorFile." linea:".$errorLine."</div>\n<br>";
        if (isset(self::$_todoElTexto))
        {
			self::$_todoElTexto .= $txe;
		}
		else{
			self::$_todoElTexto = $txe;
		}
		
        if (isset(self::$_toStringException))
        {
            $exception = self::$_toStringException;
            // Always unset '_toStringException', we don't want a straggler to be 
            // found later if something came between the setting and the error
            self::$_toStringException = null;
            // echo "-----------".$errorMessage."----------------";
            // solamente apara el metodo __toString y deve ser un problema con el string value.
            if (preg_match('~^Method .*::__toString\(\) must return a string value$~', $errorMessage))
                throw $exception;
        }
        
        return false;
    }
	
	public static function mostrar(){
		// si existen errores mostrarlos.
		return isset(self::$_todoElTexto );
	}
	
    public static function throwToStringException($exception)
    {
        // Should not occur with prescribed usage, but in case of recursion: clean out exception, 
        // return a valid string, and weep
        if (isset(self::$_toStringException))
        {
            self::$_toStringException = null;
            return '';
        }

        self::$_toStringException = $exception;

        return null;
    }

     public static function salida(){
		
		return self::$_todoElTexto ;
	}
	
	public static function gestorErrores($númerr, $menserr, $nombrearchivo, $númlínea, $vars) 
	{
		// marca de tiempo para la entrada del error
		$fh = date("Y-m-d H:i:s (T)");

		// definir una matriz asociativa de cadena de error
		// en realidad las únicas entradas que deberíamos
		// considerar son E_WARNING, E_NOTICE, E_USER_ERROR,
		// E_USER_WARNING y E_USER_NOTICE
		$tipoerror = array (
					E_ERROR              => 'Error',
					E_WARNING            => 'Warning',
					E_PARSE              => 'Parsing Error',
					E_NOTICE             => 'Notice',
					E_CORE_ERROR         => 'Core Error',
					E_CORE_WARNING       => 'Core Warning',
					E_COMPILE_ERROR      => 'Compile Error',
					E_COMPILE_WARNING    => 'Compile Warning',
					E_USER_ERROR         => 'User Error',
					E_USER_WARNING       => 'User Warning',
					E_USER_NOTICE        => 'User Notice',
					E_STRICT             => 'Runtime Notice',
					E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
					);
		// conjunto de errores por el cuál se guardará un seguimiento de una variable
		$errores_usuario = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

		// obteniendo el rastreo.
		ob_start();
			debug_print_backtrace();
			$trace = ob_get_contents();
        ob_end_clean();

        // Remove first item from backtrace as it's this function which
        // is redundant.
        $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);
        // Renumber backtrace items.
        // $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); 
        $rastreo = explode("\n",$trace);
        
		$err = "<div><errorentry>\n";
		$err .= "\t<strong><datetime>" . $fh . "</datetime></strong>\n";
		$err .= "\t<errornum>" . $númerr . "</errornum>\n";
		$err .= "\t<errortype>" . $tipoerror[$númerr] . "</errortype>\n";
		$err .= "\t<samp><errormsg>" . $menserr . "</errormsg></samp>\n";
		$err .= "\t<scriptname>" . $nombrearchivo . "</scriptname>\n";
		$err .= "\t<scriptlinenum>" . $númlínea . "</scriptlinenum>\n";
		$err .= "\t<ul visible=\"hidden\" style=\"display:none\" >\n\t\ŧ<li>". implode("</li>\n\t\t<li>",$rastreo)."</li>\n</ul>\n";
		
		if (in_array($númerr, $errores_usuario)) {
			$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
		}
		$err .= "</errorentry></div>\n\n";
		
		// para probar
		// echo $err;

		// guardar al registro de errores, y enviarme un e-mail si hay un error crítico de usuario
		// error_log($err, 3, PATH."/aux/error.log");
		// if ($númerr == E_USER_ERROR) {
		//    mail("phpdev@example.com", "Error Crítico de Usuario", $err);
		// }
		if (debugmode){
			// si no esta el modo de depuracion no se agregan errores.
			$registro = file_get_contents(PATH ."/aux/error.log");
			$f=file_put_contents(PATH ."/aux/error.log" , $err . $registro );
		
		}
	}
	
	
}

/*
// ejemplo:

class My_Class
{
    public function doComplexStuff()
    {
		// genera un error aproposito.
        throw new Exception('UN ERROR!');
    }

    public function __toString()
    {
        try
        {
            // intentar hacer algo que falle.
            return $this->doComplexStuff();
        }
        catch (Exception $e)
        {
            // The 'return' is required to trigger the trick
            // retorna un auliar.
            return My_ToStringFixer::throwToStringException($e);
        }
    }
}

$x = new My_Class();

try
{
    echo $x;
}
catch (Exception $e)
{
    echo 'obtuvimos la exepcion! : '. $e.' con este mensaje' ;
}

?>
*/
