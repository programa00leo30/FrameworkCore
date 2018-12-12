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
		$this->pagina["ayuda"] = new AyudaVistas();
		$this->pagina["html"] = new htmlinput();
		$this->pagina["datos"] = $datos;
		$this->modelo = $modelo;

	}

	public function favicon(){
		echo "favicon.ico";
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
		$this->entrada("plantilla",$archivo);//$this->pagina["archivo"]);
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
		Debuger::log("tipo", array("","<!-- df -->","<!-- 404 $archivo -->")[$this->modelo->falla()] );

		require_once($file);

	}
	public function render(){
		// $vista= "index";
		$this->entrada("plantilla",$this->pagina["archivo"],$this->pagina["datos"]);
		// require_once PATH.'/plantilla/'.$this->pagina["archivo"] ;
		if (debugmode){
			// el signo + viene como un espacio.
			// echo "<div>".nz($_GET["dg"])."</div>";
			if (isset($_GET["dg"])){
				$msg=base64_decode( str_replace(" ","+",nz($_GET["dg"],"") ) );
				echo "<div class='falla'>$msg</div>";
			}
		}

	}
	/*
	private function modelo(){
		global $modelo;
		return $modelo;
	}
	*/
}
