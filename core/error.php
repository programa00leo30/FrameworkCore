<?php
define("COREVERSION","3");
define("COREREVISION","3");
define("COREACTUALIZACION","1");

define("CORE",COREVERSION.".".COREREVISION.".".COREACTUALIZACION);

function ControlCierre() {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;
	if ( DebugerCore::showlog() )
	{
		$error = error_get_last();
		if( !is_null($error) ){ // !== NULL) {
			$errno   = $error["type"];
			$errfile = $error["file"];
			$errline = $error["line"];
			$errstr  = $error["message"];
			MiControlError::errorHandler( $errno, $errstr, $errfile, $errline);
			echo MiControlError::mostrar();
			ChromePhp::log("cierre_ERROR:", "error:$errno, $errstr, $errfile, $errline" );
			echo "error:$errno, $errstr, $errfile, $errline";
			
			DebugerCore::render(); 	
			// DebugerCore::render(); 	
			echo "<div style='color:red' > fin con errores.</div>";
		}else{
			// muestra errores:
			
			Debuger::render(); 	
			// echo (MiControlError::salida())?MiControlError::salida():"" ;
			echo "<!-- fin sin errores.-->";
		}
	}
	// cierre de pagina.

}

// MiControlError::gestorErrores($númerr, $menserr, $nombrearchivo, $númlínea, $vars)
class MiControlError
{
    protected static $_toStringException;
    protected static $_todoElTexto="";
    protected static $_todoElError="";
    protected static $_contador=0;
    protected static $_barraColocada=false;

    public static function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
    {
		self::$_contador++;
		$_t=explode("\n",$errorMessage);
		foreach($_t as $k=>$v)$_t[$k]= "<li>$v</li>\n";
		$txe= "<div>
			<h3>(".$errorNumber.")ERROR:</h3> en archivo:".
			$errorFile." linea:".$errorLine.
			"\n<div><error>\n".
			 "<ul>".implode("",$_t) ."</ul>".
			"\n</error></div> \n </div>\n<br>";
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
		return isset(self::$_todoElError )?self::$_todoElError:"--";
	}

	public static function contador(){
		return self::$_contador;
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
	public static function colocarBarra($auxiliar=""){
		if (!self::$_barraColocada){
		self::$_barraColocada=true;
		return "<div id=barraerror $auxiliar ></div>";
		}else return "";
	}
     public static function salida(){
		 if(debugmode)
			if (self::$_contador > 0 )
			{
				$barra=self::colocarBarra();

		$_t=<<<FUNC
			$barra
		<script>

			function ver_error(id){
				var elem = document.getElementById("error_"+id);
				if ( elem.style["display"] == "none" ){
					elem.style="display:bock;";
				}else{
					elem.style="display:none;";
				}
			};

			function mostrar_error(texto){
				var barra=document.getElementById("barraerror");
				/* decodificar base64encode */
				barra.innerHTML = barra.innerHTML + unescape(atob(texto)) ;
			};

		</script>
FUNC;
		global $debug;
		$debug->error( self::$_todoElTexto ."////" );
		return true; //self::$_contador;
		// return $_t . self::$_todoElTexto .self::$_contador;
		}else return false; // self::$_contador;

	}

	public static function gestorErrores($númerr, $menserr, $nombrearchivo, $númlínea, $vars)
	{
		if ( DebugerCore::showlog() ) 
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
		/*
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
        // quitar los errores posibles de comillado.
        $trace=str_replace("'",'"',$trace);
        $trace=str_replace("\r",'<br>',$trace);

        $rastreo = explode("\n",$trace);
        // quitar posibles caracteres de error:
        foreach($rastreo as $k=>$v) $rastreo[$k] = htmlentities(utf8_encode($v));
		*/
		$rastreo = debug_backtrace(6);
		
        $_contador= ( self::$_contador ++ );
		$rast="";
		foreach($rastreo as $k=>$v){
			// $convercion = self::_convertir($v);
			foreach ( array("file","line","function") as $v){
			$convercion[$v]= isset($v[$v])?$v[$v]:"no $v";
			}
			$rast .= "</li>". implode(", ",$convercion ) ."<li>\n";
			$rastreo[$k] = implode(", ",$convercion );
		}
		$err = "<div style='block'><errorentry>";
		$err .= "\t<strong><datetime>" . $fh . "</datetime></strong>";
		$err .= "\t<errornum>" . $númerr . "</errornum>";
		$err .= "\t<errortype>" . $tipoerror[$númerr] . "</errortype>";
		$err .= "\t<samp><errormsg>" . str_replace("'",'"',$menserr) . "</errormsg></samp>";
		$err .= "\t<scriptname>" . $nombrearchivo . "</scriptname>";
		$err .= "\t<scriptlinenum>" . $númlínea . "</scriptlinenum>";
		$err .= "\t<a href=\"#\" onClick=\"ver_error(%27" . $_contador . "%27);\" >#</a>";
		$err .= "\t<ul visible=\"hidden\" style=\"display:none\" id=\"error_".$_contador."\"><li>".
			// implode("</li><li>",$rastreo)
			$rast ."</li></ul>";
		$rastr="";
		// var_dump($rastreo);
		foreach($rastreo as $v) {
			$rastr.= "<li>\n<script> document.write( atob('".base64_encode($v)."'));</script>\n</li>";

		}
		$rastr="";
		$err1 = "<div style='block'><errorentry>";
		$err1 .= "\t<strong><datetime>" . $fh . "</datetime></strong>\n";
		$err1 .= "\t<errornum>" . $númerr . "</errornum>\n";
		$err1 .= "\t<errortype>" . $tipoerror[$númerr] . "</errortype>\n";
		$err1 .= "\t<samp style='color:black'><errormsg>" . str_replace("'",'"',$menserr) . "</errormsg></samp>\n";
		$err1 .= "\t<div class='block'><scriptname>" . $nombrearchivo . "</scriptname>\n";
		$err1 .= "\t<scriptlinenum>" . $númlínea . "</scriptlinenum></div>\n";
		$err1 .= "\t<ul id=\"error_".$_contador."\">\n\t\t".
			$rastr."\n</ul>";

		if (in_array($númerr, $errores_usuario)) {
			ob_start();
				var_dump($vars);
				$vars = ob_get_contents();
			ob_end_clean();

			$err .= "\t<vartrace>"
			//. wddx_serialize_value($vars, "Variables")
			. $vars
			. "</vartrace>";
		}
		$err .= "</errorentry></div>";

		if (debugmode){
			// si no esta el modo de depuracion no se agregan errores.
			// $registro = file_get_contents(PATH ."/auxiliar/error.log");
			// $f=file_put_contents(PATH ."/auxiliar/error.log" , $err . $registro );
			self::$_todoElTexto=
				self::$_todoElTexto.
				"<script>
				mostrar_error('".
				base64_encode($err).
				"')\n </script>\n ";
			self::$_todoElError .= "<div style='color:red' > $err1 </div>";
			global $debug;
			$debug->error($err);
		}
	
		} // mostrar errores.
	}

	public function __toString(){
		return $this->salida();
	}
	
	private static function _convertir($object)
    {
        // if this isn't an object then just return it
        if (!is_object($object)) {
            return $object;
        }
        //Mark this object as processed so we don't convert it twice and it
        //Also avoid recursion when objects refer to each other
        $object_as_array = array();
        // first add the class name
        $object_as_array['___class_name'] = get_class($object);
        // loop through object vars
        $object_vars = get_object_vars($object);
        foreach ($object_vars as $key => $value) {
            // same instance as parent object
            if ($value === $object || in_array($value, $this->_processed, true)) {
                $value = 'recursion - parent object [' . get_class($value) . ']';
            }
            $object_as_array[$key] = $this->_convert($value);
        }
        $reflection = new ReflectionClass($object);
        // loop through the properties and add those
        foreach ($reflection->getProperties() as $property) {
            // if one of these properties was already added above then ignore it
            if (array_key_exists($property->getName(), $object_vars)) {
                continue;
            }
            $type = $this->_getPropertyKey($property);
            if ($this->_php_version >= 5.3) {
                $property->setAccessible(true);
            }
            try {
                $value = $property->getValue($object);
            } catch (ReflectionException $e) {
                $value = 'only PHP 5.3 can access private/protected properties';
            }
            // same instance as parent object
            if ($value === $object || in_array($value, $this->_processed, true)) {
                $value = 'recursion - parent object [' . get_class($value) . ']';
            }
            $object_as_array[$type] = $this->_convert($value);
        }
        return $object_as_array;
    }
}


class tiempo{
	private static $iniTimer;
	private static $_texto;
	public function __construct(){
		self::$iniTimer = microtime(true);
	}
	public function __call($name,$arg){
		echo "<div>(".round( microtime(true) - self::$iniTimer ,5).")archivo:".$arg[0]." linea:".$arg[1]."</div>\n";
	}
	public function s($texto){
		self::$_texto = "<scrip> mostrar_error('".
			round( microtime(true) - self::$iniTimer ,5) .$texto.
			"');</scrip>";
	}
} ;



