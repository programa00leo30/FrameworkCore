<?php
if (!defined("debugmode")){

	define("debugmode",false);
}
// else




/* ******************************************************
 * funcion especial para enviar parametros al log del navegador
 * ejemplo  de uso:
 * include 'ChromePhp.php';
ChromePhp::log('Hello console!');
ChromePhp::log($_SERVER);
ChromePhp::warn('something went wrong!');
*
/* ******************************************************/

/**
 * Copyright 2010-2013 Craig Campbell
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Server Side Chrome PHP debugger class
 *
 * @package ChromePhp
 * @author Craig Campbell <iamcraigcampbell@gmail.com>
 */
class ChromePhp
{
    /**
     * @var string
     */
    const VERSION = '4.1.0';
    /**
     * @var string
     */
    const HEADER_NAME = 'X-ChromeLogger-Data';
    /**
     * @var string
     */
    const BACKTRACE_LEVEL = 'backtrace_level';
    /**
     * @var string
     */
    const LOG = 'log';
    /**
     * @var string
     */
    const WARN = 'warn';
    /**
     * @var string
     */
    const ERROR = 'error';
    /**
     * @var string
     */
    const GROUP = 'group';
    /**
     * @var string
     */
    const INFO = 'info';
    /**
     * @var string
     */
    const GROUP_END = 'groupEnd';
    /**
     * @var string
     */
    const GROUP_COLLAPSED = 'groupCollapsed';
    /**
     * @var string
     */
    const TABLE = 'table';
    /**
     * @var string
     */
    protected $_php_version;
    /**
     * @var int
     */
    protected $_timestamp;
    /**
     * @var array
     */
    protected $_json = array(
        'version' => self::VERSION,
        'columns' => array('log', 'backtrace', 'type'),
        'rows' => array()
    );
    /**
     * @var array
     */
    protected $_backtraces = array();
    /**
     * @var bool
     * si envia o no el header.
     */
    protected static $_error_triggered = debugmode;
    /**
     * @var array
     */
    protected $_settings = array(
        self::BACKTRACE_LEVEL => 1
    );
    /**
     * @var ChromePhp
     */
    protected static $_instance;
    /**
     * Prevent recursion when working with objects referring to each other
     *
     * @var array
     */
    protected $_processed = array();
    /**
     * al ejecutar render envia todas las head formadas.
     *
     * @var array
     */
    protected $_headers = array();
    /**
     * constructor
     */
    private function __construct()
    {

        $this->_php_version = phpversion();
        $this->_timestamp = $this->_php_version >= 5.1 ? $_SERVER['REQUEST_TIME'] : time();
        $this->_json['request_uri'] = $_SERVER['REQUEST_URI'];
		$this->_headers = array();

    }
    /**
     * gets instance of this class
     *
     * @return ChromePhp
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * logs a variable to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function Debugerlog()
    {
        $args = func_get_args();
        return self::_log('DEBUGGER:', $args,true);
    }
    /**
     * logs a variable to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function log()
    {
        $args = func_get_args();
        return self::_log('', $args);
    }
    /**
     * logs a warning to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function warn()
    {
        $args = func_get_args();
        return self::_log(self::WARN, $args);
    }
    /**
     * logs an error to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function error()
    {
        $args = func_get_args();
        return self::_log(self::ERROR, $args);
    }
    /**
     * sends a group log
     *
     * @param string value
     */
    public static function group()
    {
        $args = func_get_args();
        return self::_log(self::GROUP, $args);
    }
    /**
     * sends an info log
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function info()
    {
        $args = func_get_args();
        return self::_log(self::INFO, $args);
    }
    /**
     * sends a collapsed group log
     *
     * @param string value
     */
    public static function groupCollapsed()
    {
        $args = func_get_args();
        return self::_log(self::GROUP_COLLAPSED, $args);
    }
    /**
     * ends a group log
     *
     * @param string value
     */
    public static function groupEnd()
    {
        $args = func_get_args();
        return self::_log(self::GROUP_END, $args);
    }
    /**
     * sends a table log
     *
     * @param string value
     */
    public static function table()
    {
        $args = func_get_args();
        return self::_log(self::TABLE, $args);
    }
    /**
     * internal logging call
     *
     * @param string $type
     * @return void
     */
    protected static function _log($type, array $args ,$debuger=false)
    {
        // nothing passed in, don't do anything
        if (count($args) == 0 && $type != self::GROUP_END) {
            return;
        }
        $logger = self::getInstance();
        $logger->_processed = array();
        $logs = array();
        foreach ($args as $arg) {
            $logs[] = $logger->_convert($arg);
        }
        $backtrace = debug_backtrace(false);
        if ($debuger) array_pop($backtrace);
        
        $level = $logger->getSetting(self::BACKTRACE_LEVEL);
        $backtrace_message = 'unknown';
        if (isset($backtrace[$level]['file']) && isset($backtrace[$level]['line'])) {
            $backtrace_message = $backtrace[$level]['file'] . ' : ' . $backtrace[$level]['line'];
        }
        $logger->_addRow($logs, $backtrace_message, $type);
    }
    /**
     * converts an object to a better format for logging
     *
     * @param Object
     * @return array
     */
    protected function _convert($object)
    {
        // if this isn't an object then just return it
        if (!is_object($object)) {
            return $object;
        }
        //Mark this object as processed so we don't convert it twice and it
        //Also avoid recursion when objects refer to each other
        $this->_processed[] = $object;
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
    /**
     * takes a reflection property and returns a nicely formatted key of the property name
     *
     * @param ReflectionProperty
     * @return string
     */
    protected function _getPropertyKey(ReflectionProperty $property)
    {
        $static = $property->isStatic() ? ' static' : '';
        if ($property->isPublic()) {
            return 'public' . $static . ' ' . $property->getName();
        }
        if ($property->isProtected()) {
            return 'protected' . $static . ' ' . $property->getName();
        }
        if ($property->isPrivate()) {
            return 'private' . $static . ' ' . $property->getName();
        }
    }
    /**
     * adds a value to the data array
     *
     * @var mixed
     * @return void
     */
    protected function _addRow(array $logs, $backtrace, $type)
    {
        // if this is logged on the same line for example in a loop, set it to null to save space
        if (in_array($backtrace, $this->_backtraces)) {
            $backtrace = null;
        }
        // for group, groupEnd, and groupCollapsed
        // take out the backtrace since it is not useful
        if ($type == self::GROUP || $type == self::GROUP_END || $type == self::GROUP_COLLAPSED) {
            $backtrace = null;
        }
        if ($backtrace !== null) {
            $this->_backtraces[] = $backtrace;
        }
        $row = array($logs, $backtrace, $type);
        $this->_json['rows'][] = $row;
        $this->_writeHeader($this->_json);
    }
	/*
    public function trigger(boolean $var){
		self::$_error_triggered = $var;
	}
	*/
	public static function trigger( ){
		$args = func_get_args();
		// echo "<!---- estado de debuger ". ( var_dump($args)) ." !>";
		if (is_bool($args[0]))
			self::$_error_triggered = $args[0];
	}
    public static function render(){
		if (self::$_error_triggered){
			/* enviar o no el informe */
			$logger = self::getInstance();
			$logger->_render();
		}
		// header(self::HEADER_NAME . ': ' . $this->_encode("test enviado"));
	}
	protected function _render(){
		foreach($this->_headers as $v)
			header($v);
	}
    protected function _writeHeader($data)
    {
		$this->getInstance();
		$this->_headers[] = self::HEADER_NAME . ': ' . $this->_encode($data);
        // header(self::HEADER_NAME . ': ' . $this->_encode($data));
    }
    /**
     * encodes the data to be sent along with the request
     *
     * @param array $data
     * @return string
     */
    protected function _encode($data)
    {
        return base64_encode(utf8_encode(json_encode($data)));
    }
    /**
     * adds a setting
     *
     * @param string key
     * @param mixed value
     * @return void
     */
    public function addSetting($key, $value)
    {
        $this->_settings[$key] = $value;
    }
    /**
     * add ability to set multiple settings in one call
     *
     * @param array $settings
     * @return void
     */
    public function addSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->addSetting($key, $value);
        }
    }
    /**
     * gets a setting
     *
     * @param string key
     * @return mixed
     */
    public function getSetting($key)
    {
        if (!isset($this->_settings[$key])) {
            return null;
        }
        return $this->_settings[$key];
    }
}

/**
 * Server Side Chrome PHP debugger class
 *
 * @package Debuger
 * @author Leandro Morala <programa00leo30@gmail.com>
 */

class Debuger
{
	private static $mostrarlog=true;
	
	public static function log(){
		if (self::showdebug()){
			$arg=func_get_args();
			// var_dump($arg);
			DebugerCore::loga($arg[0],$arg[1]);
		}
	}
	public static function msg(){
		$arg=func_get_args();
		DebugerCore::loga($arg[0],$arg[1]);
	}
	
	public static function render(){
		if (self::showdebug())
			DebugerCore::render();
	}
	public static function nolog(){
		DebugerCore::nolog();
	}
	
	public static function showdebug(){
		$rt=false;
		if (defined("debugmode"))$rt=true;
		$rt= $rt or self::$mostrarlog ;
		return $rt;
	}

}


class DebugerCore 
{	
	private static $mostrarlog = true;
	private static $_mostrarlogTEXTO = "";
	
    public static function Register()
    {
	self::$_mostrarlogTEXTO = new html("div",['class'=>"debugcore"]);
	if (debugmode)
		{
			// modalidad de depuracion.
			// error_reporting  (E_ALL);
			// ini_set ('display_errors', true);
			// set_error_handler(array('MiControlError', 'errorHandler'));
			// cambiando control de errores:
			/* utilizar herramientas de tiempo en modo depurador:*/
			self::SeguroRegister();

		}else{
			// esta en produccion:
			self::NoSeguroRegister();
		}
	}
	public static function NoSeguroRegister(){
		set_error_handler(array('MiControlError', 'gestorErrores'),(E_ALL | E_STRICT));
		register_shutdown_function( "ControlCierre" );
	}

	public static function SeguroRegister(){
			set_error_handler(array('MiControlError', 'gestorErrores'),( E_ALL | E_STRICT) );
			// error_reporting(E_ALL | E_STRICT);
			register_shutdown_function( "ControlCierre" );
	}

	public static function render(){
		// para mostrar el texto al final
		
		//if (self::showdebug() )
		if ( PHP_SAPI <>"CLI" ) // ; // devuelve CLI o apache2handler
		{
			echo "<div>
			<style>
				.debug{
					background-clip: border-box;
					line-height: 23px;
					background-attachment: scroll;
					block; 
					color:black;
					padding-top: 0px;
					padding-right: 0px;
					padding-left: 0px;
					border-top-width: 1px;
					border-top-style: solid;
					border-right-width: 1px;
					border-right-style: solid;
					border-left-width: 1px;
					border-left-style: solid;
					border-bottom-style: solid;
					border-bottom-width: 0px;
					margin-top: 2px;
				}
				file{
					color:#03ff00;
					
				}
			</style>
			mostrar depurador:";
			echo self::$_mostrarlogTEXTO;
			echo "fin de datos:</div>";
		}else
			echo self::$_mostrarlogTEXTO;
	}
	public static function showdebug(){
		$rt=false;
		if (defined("debugmode"))$rt=true;
		$rt= $rt or self::$mostrarlog ;
		return $rt;
	}
	
	public static function showlog(){
		return self::$mostrarlog;
	}
	
	public static function nolog(){
		self::$mostrarlog =false;
	}
	
	public static function loga(){
		self::_log();
	}
	public static function log(){
		if (self::showdebug())
			self::_log();
	}
	
	private static function _log(){
		// echo "pasa por aqui<br>\n";
		// debug_print_backtrace();
		$ver=debug_backtrace();
		foreach ($ver as $k=>$v){
			// quitamos este propio archivo.
		 if (isset($v["file"]) and ($v["file"]==__file__) )array_shift($ver);
		}
		
		$file = $ver[0]["file"];
		$file1 = $ver[1]["file"];
		$line = $ver[0]["line"];
		$line1 = $ver[1]["line"];
		$extra="llamado desde $file1 ($line1)";
		$args = func_get_args();
		// var_dump($args);
		// var_dump($ver);
		if (count($args) != 2){
			// self::$_mostrarlogTEXTO .= "<!-- sin argumentos linea:$line archivo:$file -->";
			self::$_mostrarlogTEXTO->add( new coment("sin argumentos linea:$line archivo:$file "));
		}else{
			$tipo=$args[0];
			$mensaje=$args[1];
			/*self::$_mostrarlogTEXTO .= "<div class='debug' style='block; color:black'><file>archivo:$file</file>
			<line>($line)$extra</line>
			<type>tipo:$tipo</type>
			<msg>msg: $mensaje </msg></div>\n";
			*/
			self::$_mostrarlogTEXTO->add( 
				new html("div",['class'=>"debug",style=>"block; color:black"] ,[
					new html("file",[],"archivo:$file")
					,new html("line",[],"($line)$extra")
					,new html("type",[],"tipo:$tipo")
					,new html("msg",[],"msg: $mensaje")
				]
			));
		}
		// echo self::$_mostrarlogTEXTO;
		//echo "pasa por aqui;$line,f:$file,t:$tipo,m:$mensaje<br>\n";
		$args[0]= "debug_log:".$args[0]; 
		ChromePhp::Debugerlog($args[0],$args[1]);
	
	}
	public static function msg(){
		
		 $args = func_get_args();
		self::log("msg",$args[0]);
	}
	public static function warn(){
		 $args = func_get_args();
		self::log("WARN",$args[0].$args[1]);
	}
}



