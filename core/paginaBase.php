<?php

/*	paginaBase.php
	control de pagina generico.
*/
class PaginaBase{
	private $modelo;
	private $pagina;
	public $title;
	public $descripcion;
	public $autor;

	// pagina = plantilla:
	// view = contenidos de la plantilla
	// datos = valores pasados a view.

	public function __construct($pagina="index",$view, $datos=array("titulo"=>"index" , "autor"=>"leandro morala" )) {
		global $modelo;

		$this->pagina["archivo"] = $pagina."Plantilla.php";
		$this->pagina["contenido"] = $view."ViewContenido.php";
		
		// Debuger::log("pagina:","contenido : ".$view."ViewContendo.php");
		
		$this->pagina["ayuda"] = new AyudaVistas();
		$this->pagina["html"] = new htmlinput();
		$this->pagina["datos"] = $datos;
		$this->modelo = $modelo;

	}

	public function favicon(){
		return "favicon.ico";
	}
	public function barra($archivo){
		/*
        // aqui esta la variable auxiliar de todos los views.
        $helper = $this->pagina["ayuda"];
		$imput = $this->pagina["html"];

		$this->modelo->setAccion("plantilla");
		$this->modelo->RequireOnce("barrasuperior.php");
		// require_once PATH.'/plantilla/barrasuperior.php';
		*/
		return $this->entrada("plantilla",$archivo);//$this->pagina["archivo"]);
	}
	public function htmlObjectContenido(){
		return $this->HTMLentrada( "view",$this->pagina["contenido"],$this->pagina["datos"] );
	}
	public function htmlObject($PaginaACargar,$plantilla="plantilla"){
		return $this->HTMLentrada($plantilla,$PaginaACargar,$this->pagina["datos"]);
	
	}
	
	private function HTMLentrada($actuador,$archivo,$arreglo=array()){

		$pagina=$this;
		$helper = $this->pagina["ayuda"];
		$html = $this->pagina["html"];

		foreach ($arreglo as $id_assoc => $valor) {
            ${$id_assoc}=$valor;
            //echo "pasando:$id_assoc == $valor<br>\n";
        }

		// echo "PaginaBase:archivo:$archivo actuador:$actuador<br>\n";
		$this->modelo->setAccion($actuador);
		// $file=$this->modelo->runing($this->pagina["archivo"]);
		$file=$this->modelo->runing($archivo);
		DebugerCore::log("pagina_file::",  
			( array(".:OK:.",".:DEF:.",".:404($archivo):.")[$this->modelo->falla()] ) 
			. "$archivo // file:$file" 
		);
		ob_start();
		$rt = require ($file);
		ob_end_clean();
		if ($rt instanceof html) return $rt;
		else {
			 // contenido texto en puro.
			 return  new coment("$archivo") ;
			// return $rt ;
		}
	}
	public function contenido(){

        $this->entrada("view",$this->pagina["contenido"],$this->pagina["datos"]);
	}
	public function piepagina(){
		$this->entrada("plantilla","footer.php");
	}
	private function entrada($actuador,$archivo,$arreglo=array()){

		$pagina=$this;
		$helper = $this->pagina["ayuda"];
		$html = $this->pagina["html"];

		foreach ($arreglo as $id_assoc => $valor) {
            ${$id_assoc}=$valor;
            //echo "pasando:$id_assoc == $valor<br>\n";
        }

		// echo "PaginaBase:archivo:$archivo actuador:$actuador<br>\n";
		$this->modelo->setAccion($actuador);
		// $file=$this->modelo->runing($this->pagina["archivo"]);
		$file=$this->modelo->runing($archivo);
		DebugerCore::log("pagina_file::",  
			( array(".:OK:.",".:DEF:.",".:404($archivo):.")[$this->modelo->falla()] ) 
			. "$archivo // file:$file" 
		);
		ob_start();
			$rt = require ($file);
			$page = ob_get_contents();
		ob_end_clean();
		/*
		echo "\n------archivo $file----------\n";
		var_dump($rt);
		echo "\n----------------\n";
		var_dump($page);
		*/
		if ($rt instanceof html) return $rt;
		else {
			 // echo "<!-- no es intanca de html -->";
			 echo $page ;
			// return $rt ;
		}
	}
	public function render(){
		// $vista= "index";
		echo $this->entrada("plantilla",$this->pagina["archivo"],$this->pagina["datos"]);
		// require_once PATH.'/plantilla/'.$this->pagina["archivo"] ;

	}

}
