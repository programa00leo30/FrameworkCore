<?php

use Dompdf\Dompdf ;

class AyudaVistas{
    private $pdf;
    
    public function url($controlador=CONTROLADOR_DEFECTO,$accion=ACCION_DEFECTO){
        $urlString=URL."index.php/".$controlador."/".$accion;
        // echo "url:".$urlString."<<<" ;
        return $urlString;
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
