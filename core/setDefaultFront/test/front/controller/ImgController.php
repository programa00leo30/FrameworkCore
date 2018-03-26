<?php
class ImgController extends ControladorBase{
     
    public function __construct() {
        parent::__construct();
    }
    
    public static function img($archivo, $parametros){
		
		$dato=base64_encode(file_get_contents(PATH."/img/".$archivo ));
		$dato=PATH."/img/".$archivo ;
		$dimension = array( "x"=>5,"y"=>5 );
		
		if (!file_exists($dato)){
			$dato=PATH."/img/logo.jpg" ; // valor por defecto.
		}
		if (isset($parametros["x"])){
			$dimension["x"]=$parametros["x"];
		}
		if (isset($parametros["y"])){
			$dimension["y"]=$parametros["y"];
		}
		$nuevo_ancho=$dimension["x"]	;
		$nuevo_alto=$dimension["y"];	
		
		$im     =  imagecreatefromjpeg($dato);
		// crear una imagen nueva 

		$thumb = imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
		imagecopyresized($thumb,$im, 0,0 ,0,0  ,$nuevo_ancho,$nuevo_alto,ImageSX($im),ImageSY($im) );
		
		ob_start();
			imagepng($thumb);
			$contenido= ob_get_contents() ;
		ob_end_clean();
		
		imagedestroy($thumb);
		imagedestroy($im);
		return $contenido ;
		
	}
		
    public function __call($name, $arguments)
	{
		$dato=base64_encode(file_get_contents(PATH."/img/".$name ));
		$dato=PATH."/img/".$name ;
		// echo $dato;
		$dimension = array( "x"=>500,"y"=>500 );
		
		if (!file_exists($dato)){
			$dato=PATH."/img/logo.jpg" ; // valor por defecto.
		}
		if (isset($_GET["x"])){
			$dimension["x"]=$_GET["x"];
		}
		if (isset($_GET["y"])){
			$dimension["y"]=$_GET["y"];
		}
		$this->view("img",array(
            "dato"=>$dato,
            "dimension"=>$dimension
        ));
	}
 
}
?>
