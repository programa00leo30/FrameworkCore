<?php
class objeto{
	private $AllColumnas;
	// objeto para la base de datos.
	
	function __construct($arg = array()) {
		foreach($arg as $k=>$v){
			$this->$k = $v;
		}
		$this->AllColumnas=$arg;
		
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
	public function __toString(){
		// generar una salida del objeto
		// como un capo de fomulario
		return $this->formulario();
	}
	public function formulario(){
		$st="";
		foreach($this->AllColumnas as $v){
			$nombre=$k;
			$st.="\t\t\t<input name=\"$v\" >\n";
		}
		return $st;
		
	}
}
