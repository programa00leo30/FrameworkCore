<?php
class JsController extends ControladorBase{
     
    public function __construct() {
        parent::__construct();
    }
    
    public function __call($name, $arguments)
	{
		$dato=PATH."/js/".$name ;
		// echo $dato;
		
		if (!file_exists($dato)){
			$dato=PATH."/js/npm.js" ; // valor por defecto.
		}
		
		$this->view("include",array(
            "dato"=>$dato,
            "tipe"=>"text/javascript"
        ));
	}
 
}
?>
