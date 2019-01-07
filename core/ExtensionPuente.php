<?php
/*
	clase especial para extender a mas de una clase.
*/

Abstract class ExtensionPuente{
	
    // array containing all the extended classes
    private $_exts = array();
    public $_this;
       
    function __construct(){$_this = $this;}
   
    public function addExt($object)
    {
		$objeto = new $object;
        $this->_exts[]["objeto"]=$objeto;
        $this->_exts[]["nombre"]=$object;
    }
   
    public function __get($varname)
    {
        foreach($this->_exts as $ext)
        {
            if(property_exists($ext["nombre"],$varname))
            return $ext->$varname;
        }
    }
   
    public function __call($method,$args)
    {
        foreach($this->_exts as $ext)
        {
            if(method_exists($ext["objeto"],$method) or is_callable(array($ext["objeto"], $method)))
            return call_user_func_array(array($ext["objeto"], $method ),$args);
        }
        throw new Exception("El Metodo ".$ext["nombre"]." {$method} no existe");
    }
   
   
}
