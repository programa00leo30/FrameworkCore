<?php

use Dompdf\Dompdf ;

class AyudaVistas{
    private $pdf;
    public $iframe;
    private $datos;
    
    public function __construct(){
		$this->iframe = false;
		
	}
	public function incluir($archivo,$carpeta){
		global $modelo,$paginaGlobal;
		// echo "PaginaBase:archivo:$archivo actuador:$actuador<br>\n";
		$modelo->setAccion($carpeta);
		// $file=$this->modelo->runing($this->pagina["archivo"]);
		$file=$modelo->runing($archivo);
		echo array("<!--helper-->","<!-- helper df -->","<!-- helper 404 $archivo -->")[$modelo->falla()];
		if ($modelo->falla() < 3)
			require_once($file);
		else
			echo "\n<div></div>\n"; // no se incluye nada.
		// return $archivo->
	}
	
    public function url($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO,$argumentos = array("ifram"=>false) ){
		// hay que agregar el scrip del que provino la peticion.
		//phpinfo();
		
		$archivo=basename($_SERVER['SCRIPT_NAME']);
		$iframe = (isset($argumentos["ifram"])?$argumentos["ifram"]:false) or $this->iframe;
		unset($argumentos["ifram"]);
		
		$get="?";
		foreach($argumentos as $k=>$v) $get.="$k=$v&";
		$get=rtrim($get,"&");
		if (strlen($get)>2)$accion.=$get;
		
		$url = URL.$archivo;
		$url = rtrim($url,"/");
		//echo $url;
		// la url debe contener el archivo destino: index.php , 
        if ( $iframe ){	
			$urlString=$url."/".$controlador."/iframe/".$accion;
		}else{
			$urlString=$url."/".$controlador."/".$accion;
		}
        return $urlString;
    }
	private function nurl($destino){
		return $this->url($this->datos["controlador"],$this->datos["acion"],$destino);
	}
		
	public function paginador($coneccion,$controlador,$acion,$paginaActual,$CantidadRegPorPagina){
		// esta funcion devuelve un paginador de inicio y por salto
		$contador = (int)($coneccion->contar()/ $CantidadRegPorPagina );
		if ($contador > 0 ){
			$this->datos["controlador"] = $controlador;
			$this->datos["acion"]=$acion;
			
			$pag=new html("div",['class'=>"col-md-12 center"]);
			$pag->add(new html("ul",['class'=>"pagination"]));
			// $pag="<div class=\"col-md-12 center\" >\n";
				$pag->ul->add(new html("li",['class'=>"disabled",id=>"IrPrimero"]
					,new html("a",[href=> $this->nurl([pag=>"first"])],"|&laquo;")));
				$pag->ul->add(new html("li",['class'=>"disabled",id=>"IrAnterior"]
					,new html("a",[href=> $this->nurl([pag=>"preview"])],"&laquo;")));
				
			if ($paginaActual > 0){
				$pag->ul->GetById("IrPrimero")->SetAtr('class',"");
				$pag->ul->GetById("IrAnterior")->SetAtr('class',"");
			}
			for ( $cont=0;$cont <= ( $contador  ); $cont++ ){
				$pag->ul->add(new html("li",[id=>"pg_$cont"],new html("a",[href=> $this->nurl([pag=>$cont])],$cont)));
			}
			$pag->ul->GetById("pg_$paginaActual")->SetAtr('class',"active");
			
			$pag->ul->add(new html("li",['class'=>"",id=>"IrSiquiente"]
				,new html("a",[href=> $this->nurl([pag=>"next"])],"&raquo;")));
			$pag->ul->add(new html("li",['class'=>"",id=>"IrUltimo"]
				,new html("a",[href=> $this->nurl([pag=>"last"])],"&raquo;|")));
			return $pag;
		}else{
			return new html("div",[]);
		}
		
	}
	// funcion que busca un valor en un arreglo.
	public function check($valor,$arreglo,$Afirmativo="",$Negativo=""){
		// var_dump($valor);
		// var_dump($arreglo);
		
		if ( in_array($valor,$arreglo)){
			return $Afirmativo ;
		}else{
			return $Negativo ;
		}
	}
    //Helpers para las vistas
    public function pdfconstruct(){
		
		// para la creacion de pdfs.
		require_once 'mods/pdf/dompdf/autoload.inc.php';
		
		
		
		$this->pdf = new  Dompdf();
		/*
		$dompdf->loadHtml($content);
		$dompdf->setPaper('A4', 'landscape'); // (Opcional) Configurar papel y orientación
		$dompdf->render(); // Generar el PDF desde contenido HTML
		$pdf = $dompdf->output(); // Obtener el PDF generado
		$dompdf->stream(); // Enviar el PDF generado al navegador
		*/
	}
	
	// para trabajar con el pdf.
	public function pdf(){
		return $this->pdf ;
	}
	
	public function barcode($texto,$filetype="PNG",
				$code="BCGcode39", $checksum="",
				$altura=25,$dpi=72,$scale=2,$rotacion=0,
				$font_family="Arial.ttf",$font_size="16"){
		
		// require_once('barcodegen.1d-php5.v5.2.1/class/BCGcode39.barcode.php');
		$rutaMod="mods/barcode/barcodegen/"; // barcodegen.1d-php5.v5.2.1
		$class_dir = $rutaMod."/class";
		
		$classFile = $code.'.barcode.php';
		$className = $code;
		$baseClassFile = 'BCGBarcode1D.php';
		$codeVersion = '5.2.0';
		
		require_once($class_dir . '/BCGColor.php');
		require_once($class_dir . '/BCGBarcode.php');
		require_once($class_dir . '/BCGDrawing.php');
		require_once($class_dir . '/BCGFontFile.php');
		
		$filetypes = array(
			'PNG' => BCGDrawing::IMG_FORMAT_PNG, 
			'JPEG' => BCGDrawing::IMG_FORMAT_JPEG, 
			'GIF' => BCGDrawing::IMG_FORMAT_GIF);
		
		/*
		
			creacion de imagen:
		*/
		$drawException = null;
		require_once($class_dir . '/'.$classFile );
		try {
			$color_black = new BCGColor(0, 0, 0);
			$color_white = new BCGColor(255, 255, 255);

			$code_generated = new $className();

			$code_generated->setScale(max(1, min(4, $scale)));
			$code_generated->setBackgroundColor($color_white);
			$code_generated->setForegroundColor($color_black);
			/*
			if ($_GET['text'] !== '') {
				$text = convertText($_GET['text']);
				$code_generated->parse($text);
			}
			*/
			$code_generated->parse($texto);
			
		} catch(Exception $exception) {
			$drawException = $exception;
		}

		$drawing = new BCGDrawing('', $color_white);
		if($drawException) {
			$drawing->drawException($drawException);
		} else {
			$drawing->setBarcode($code_generated);
			$drawing->setRotationAngle($rotacion);
			// $drawing->setDPI($_GET['dpi'] === 'NULL' ? null : max(72, min(300, intval($_GET['dpi']))));
			// entre 72 y 300
			$drawing->setDPI($dpi);
			$drawing->draw();
		}

		ob_start();
			$drawing->finish( $filetypes[$filetype] );
			$contenido= ob_get_contents() ;
		ob_end_clean();
		return $contenido;
		
	}
}
?>
