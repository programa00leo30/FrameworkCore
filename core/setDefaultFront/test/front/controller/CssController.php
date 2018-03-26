<?php
class CssController extends ControladorBase{
     
    public function __construct() {
        parent::__construct();
    }
    
    public function __call($name, $arguments)
	{
		$dato=PATH."/css/".$name ;
		// echo $dato;
		
		if (!file_exists($dato)){
			$dato=PATH."/css/estilos.css" ; // valor por defecto.
		//if (substr_compare($dato,"_thumbnail")) {
			// quiere hacer un redimencionamiento.
		//	str_replace("_thumbnail","",$dato);
		//	}
		}
		
		$this->view("include",array(
            "dato"=>$dato,
            "tipe"=>"text/css"
        ));
	}
 
}
?>
