<?php

class ControlArchivo{

	/*
	 * la estructura general es de la forma:
	 * /[camino]/index.php
	 * /[camino]/[modelo]/[estructVisual]/[condicion]/[estructActual]/[archivo]
	 * haciendo de esta forma una estructura ordenada:
	 * y gerarquica.
	 * por defocto dentro de estructVisual debe existir la carpeta default.
	 * condicion = setCondicion
	 * estructVisual = setVisual
	 * modelo = setModelo
	 * estructAcutal = setActuador ( ej: css, controller, js, view, plantilla, etc. )
	 * 
	 */
	private $arreglo;
	private static $camino;
	private static $modelo;
	private static $condicion;
	private static $estructVisual;	// estructura que se visualiza.
	private static $estructActual;	// estructura que realiza acciones.
	
	public function __construct($path=""){
		//obtenemos el path usado..
		if ($path=="")
			$this->arreglo = explode("/",PATH);
		else
			$this->arreglo = explode("/",$path);
			// $this->arreglo = explode("/",pathinfo( __file__, PATHINFO_DIRNAME));
		$count = count($this->arreglo);
		self::$condicion = array_pop($this->arreglo);
		// $this->estructActual = array_pop($this->arreglo);
		self::$estructVisual = array_pop($this->arreglo);
		self::$modelo = array_pop($this->arreglo);
		self::$camino = implode("/",$this->arreglo);
		
		// /home/leandro/www/contable/admin/front/config
		// [camino logico]/[modelo a usar]/[estructura visual]/[ estructura actual]
		
	}
	public function RequireOnce($archivo,$default){
		// retorna el archivo necesario.
		$t= self::$camino."/".self::$modelo."/"	.self::$estructVisual."/".self::$condicion."/"		.self::$estructActual."/".$archivo;
		$d= self::$camino."/".self::$modelo."/"	.self::$estructVisual."/default/"					.self::$estructActual."/".$archivo;
		$f=self::$camino."/".self::$modelo."/"	.self::$estructVisual."/default/"					."404/$default";
		/*
		 echo "controlArchivo $t\n"
			." modelo:".$this->modelo 
			." visual:".$this->estructVisual
			." condicion:".$this->condicion
			." actual:".$this->estructActual
			." archivo:".$archivo."<br>\n";
		// */
		if ($this->fileexists($t)){
			$this->falla=0;
			return $t;
		}elseif ($this->fileexists($d)){
			$this->falla=1;
			return $d;
		}else{
			$this->falla=2;
			return $f;
		
		}
	}
	public function falla(){
		return $this->falla;
	}
	public function MiArchivo($archivo,$condicion){
		self::setActuador($condicion);
		return self::runing($archivo);
	}
	public function runing($archivo,$default="404View.php"){
		
		// return "data://text/plain;base64,".base64_encode($this->RequireOnce($archivo)) ;
		$t= self::RequireOnce($archivo,$default) ;
		Debuger::msg("ruta:$t<br>\n");
		return $t;
		
	}
	public function setCondicion($condicion){
		self::$condicion = $condicion;
	}
	public function setPerfil($perfil){
		self::$camino = $perfil;
	}
	public function setModelo($modelo){
		self::$modelo = $modelo;
	}
	public static function setVisual($Visual){
		self::$estructVisual = $Visual;
	}
	public static function setActuador($actuador){
		self::$estructActual = $actuador;
	}
	
	public static function verificar($archivo1,$archivo2){
		if ($this->fileexists($archivo1)){
			return $archivo1;
		}elseif ($this->fileexists($archivo2)){
			return $archivo2;
		}else{
			return false;
		}
	}
	public function RutaVista(){
		return self::$camino."/".self::$modelo."/"	.self::$estructVisual."/".self::$condicion;
	}
	public function rutaarchivo($nombreArchivo){
		$t= implode("/",self::$camino)."/".self::$modelo."/".self::$estructVisual."/".self::$estructActual."/".$nombreArchivo;
		$d= implode("/",self::$camino)."/".self::$modelo."/default/".self::$estructActual."/".$nombreArchivo;
		return self::verificar($t,$d);
	}
	public static function Exite($nombreArchivo){
		$t= implode("/",self::$camino)."/".self::$modelo."/".self::$estructVisual."/".self::$estructActual."/".$nombreArchivo;
		return self::fileexists($t);
	}
	private static function fileexists($archivo){
		if (file_exists($archivo)){
			return true;
		}else{
			return false;
		}
	}
	/*
	 * como respuesta un arreglo:
	 * $rt = $direciones->guardar("imgUploads",$_FILES,"documento_");
	 * $rt[0] = array($nombre,$tipo,$fecha) ;
	 * en caso de falla $nombre se vuelve false 
	 */
	public function guardar ($carpeta,$archivo,$agregado){
		$rt=false;
		foreach($archivo as $k=>$v){
			
			if ($v["error"]>0){
				// existe un error
				$destino = false ;
				$tipo = $v["error"];
				$r=false;
			}else{
				$archivo = $v["tmp_name"];
				$size = $v["size"];
				$tipo = $v["type"];
				$nombre_org = $nombre = $v["name"]; // cambio de nombre.
				$tip = explode("/",$tipo);
				// $nombre = $agregado.$v["name"].
				if ($tip[0] == "image" ){
					$nombre = $agregado.base64_encode( rand(100000,999999) ).".".$tip[1];
				
					$destino = self::$camino."/$carpeta/$nombre";
					
					// mover el archivo de temporario a final
					try {
						move_uploaded_file("$archivo","$destino");
						$r=true;
					}catch (Exception $e){
						$r=false;
					}
				}
				else{
					// no es un archivo de imagen
					$r=false;
				}
			}
			$rt[] = array($r,"nombre"=>$nombre_org,"destino"=>$destino,"tipo"=>$tipo,"size"=>$size);
		}
		return $rt;
	}
	public function recuperar($archivo){
		if($this->fileexists($archivo)){
			return file_get_contents($archivo);
		}else{
			return false;
		}
	}
		
	
}
