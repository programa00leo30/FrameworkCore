<?php
class objeto{

	// objeto para la base de datos.
	
	function __construct($arg = array()) {
		foreach($arg as $k=>$v){
			$this->$k = $v;
		}
	}
	
	function __get($campo){
		if (!isset($this->$campo)){
			return "-no data-";
		}else{
			return $this->$campo;
		}
	}
	function __set($campo,$valor){
		
		$this->$campo = $valor;
		return true;
	}
	public function __call($name, $arguments)
    {
        // Nota: el valor $name es sensible a mayúsculas.
        // echo "Llamando al método de objeto '$name' "
        //     . implode(', ', $arguments). "\n";
		return "";
    }

}
