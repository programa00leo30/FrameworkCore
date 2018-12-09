<?php
/*
 * gestor automatico de carga de archivos.
 * tanto para los archivos subidos al servidor
 * como que archivo utilizar en el framework
 *
 * UTILIZAMOS CONSTANTE PREDEFINIDA : DIRECTORY_SEPARATOR
 * que indica el tipo correcto de separador para la ruta.
 *
 */
class ControlArchivo{

	/*
	 * la estructura general es de la forma:
	 * /[camino]/index.php
	 * /[camino]/[modelo]/[estructVisual]/[condicion]/[estructActual]/[archivo]
	 * el camino aqui puede cambiar.
	 * /[camino]/[perfil]/[modelo]/[accion]/[archivo]
	 * haciendo de esta forma una estructura ordenada:
	 * y gerarquica.
	 * por defocto dentro de estructVisual debe existir la carpeta default.
	 * por defocto dentro de accion debe existir la carpeta default.
	 * condicion = setCondicion
	 * estructVisual = setVisual
	 * modelo = setModelo
	 * estructAcutal = setActuador ( ej: css, controller, js, view, plantilla, etc. )
	 *
	 */
	/**
	* @var array
	*/
	private $arreglo;
	/*
	// la ruta contiene toda la ruta base hasta donde se encuentran los perfiles.
	// perfil es el nombre que se utilizara para todo proposito.
	// modelo es el nombre de la carpeta dentro del perfil a utilizar
	// la accion es carpeta de actividad ej: controller, view, plantilla, js, etc..
	*/
	
	/**
	* @var string
	*/
	private static $ruta,$perfil,$modelo,$accion ;
	/**
	* constructor de la clase
	*
	* @param path= camino fisico de la ruta a los archivos hasta el peril inclusive.
	* @return void
	*/
	public function __construct($path=""){
		//obtenemos el path usado..
		if ($path==""){
			// utilizamos variable constante pre definida.
			$this->arreglo = explode("/",PATH);
		}else{
			// utilizamos el valor pasado como parametro.
			$this->arreglo = explode("/",$path);
			// $this->arreglo = explode("/",pathinfo( __file__, PATHINFO_DIRNAME));
		}
		$count = count($this->arreglo);

		self::$modelo = array_pop($this->arreglo);
		self::$perfil = array_pop($this->arreglo);
		self::$ruta = implode(DIRECTORY_SEPARATOR,$this->arreglo);
	}
	/**
	* funcion que devuelve la ruta logica a un archivo teniendo en cuenta perfil, modelo, y accion deseada
	*
	* @param $archivo $default  nombre del archivo deseado y otro en caso de no existir el primero.
	* @return String
	* 
	*/
	public function RequireOnce($archivo,$default){
		// retorna la ruta y archivo esperado.
		$ruta=self::$ruta.DIRECTORY_SEPARATOR .self::$perfil.DIRECTORY_SEPARATOR ;

		$t= $ruta .self::$modelo.DIRECTORY_SEPARATOR .self::$accion .DIRECTORY_SEPARATOR.$archivo; // si existe ok.
		$d= $ruta ."default"	.DIRECTORY_SEPARATOR .self::$accion .DIRECTORY_SEPARATOR.$archivo; // si no existe en el modelo.
		$f= $ruta ."default"	.DIRECTORY_SEPARATOR ."404"			.DIRECTORY_SEPARATOR.$default; // si no esta en default.

		if (self::fileexists($t)){
			// la mejor salida. todo ok.
			$this->falla=0;
			return $t;
		}elseif (self::fileexists($d)){
			// falla pero tolerable. esta en default
			$this->falla=1;
			return $d;
		}else{
			// falla terrible no esta el archivo.
			$this->falla=2;
			return $f;
		}
	}
	/**
	* retorna si exitio una falla en la ultima llamada
	*
	* @return Boolean
	*/
	public function falla(){
		return $this->falla;
	}
	/**
	* retorna una ruta a un archivo determinado de una accion tal.
	*
	* @param $modelo , $archivo  .... modelo y nombre de archivo.
	* @return String
	*/
	public function MiArchivo($modelo,$archivo){
		self::setAccion($modelo);
		return self::RequireOnce($archivo,"404View.php");
	}
	/**
	* abstaccion de RequireOnce
	*
	* @param $archvio, $default .... nombre del archivo y en caso de falla nombre a quien dirigirse.
	* @return String
	*/	
	public function runing($archivo,$default="404View.php"){
		// return "data://text/plain;base64,".base64_encode($this->RequireOnce($archivo)) ;
		return self::RequireOnce($archivo,$default) ;
	}

	public static function setPerfil($perfil){ self::$perfil=$perfil ; }
	public static function setModelo($modelo){ self::$modelo=$modelo ; }
	public static function setAccion($accion){ self::$accion=$accion ; }

	// funcion de comparacion entre dos archivos.
	public static function verificar($archivo1,$archivo2){
		if ($this->fileexists($archivo1)){
			return $archivo1;
		}elseif ($this->fileexists($archivo2)){
			return $archivo2;
		}else{
			return false;
		}
	}
	/**
	* Retorna la ruta completa a una carpeta del sistema actual.
	*
	* @return String
	*/
	public function RutaVista($acion=""){
		if ($acion=="")$acion=self::$accion;
		$acion.= DIRECTORY_SEPARATOR;
		// return self::$camino.DIRECTORY_SEPARATOR.self::$modelo."/"	.self::$estructVisual.DIRECTORY_SEPARATOR.self::$condicion;
		return
			self::$ruta.DIRECTORY_SEPARATOR
				.self::$perfil.DIRECTORY_SEPARATOR
				.self::$modelo.DIRECTORY_SEPARATOR
				.$acion;
	}

	public function rutaarchivo($nombreArchivo){
		return self::RequireOnce($nombreArchivo);
		/*
		// $t= implode(DIRECTORY_SEPARATOR,self::$camino).DIRECTORY_SEPARATOR.self::$modelo.DIRECTORY_SEPARATOR.self::$estructVisual.DIRECTORY_SEPARATOR.self::$estructActual.DIRECTORY_SEPARATOR.$nombreArchivo;
		$ruta=implode(DIRECTORY_SEPARATOR,self::$ruta).DIRECTORY_SEPARATOR.self::$perfil;
		$t= $ruta.DIRECTORY_SEPARATOR.self::$modelo.DIRECTORY_SEPARATOR.self::$accion.DIRECTORY_SEPARATOR.$nombreArchivo;
		$d= $ruta.DIRECTORY_SEPARATOR."default".self::$accion.DIRECTORY_SEPARATOR.$nombreArchivo;
		return self::verificar($t,$d);
		*/
	}
	/**
	* retorna la existencia de un archivo determinado.
	*
	* @param $nombreArchivo
	* @return Boolean
	*/
	public static function Existe($nombreArchivo){
		// funcion de comprobacion de archivo.
		// $t= implode(DIRECTORY_SEPARATOR,self::$camino).DIRECTORY_SEPARATOR.self::$modelo.DIRECTORY_SEPARATOR.self::$estructVisual.DIRECTORY_SEPARATOR.self::$estructActual.DIRECTORY_SEPARATOR.$nombreArchivo;
		return self::fileexists(
			self::$ruta.DIRECTORY_SEPARATOR
			.self::$perfil.DIRECTORY_SEPARATOR
			.self::$modelo.DIRECTORY_SEPARATOR
			.self::$accion.DIRECTORY_SEPARATOR
			.$nombreArchivo
		);
	}
	/**
	* Funcion privada para saber la existencia real de un archivo 
	* Se debe incluir la ruta al archivo.
	*
	* @param $archivo ..... archivo y ruta 
	* @return Boolean
	*/
	private static function fileexists($archivo){
		//echo "fileexist:$archivo";
		if (file_exists($archivo)){
			//echo " si<br>\n";
			return true;
		}else{
			//echo " no<br>\n";
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
		/*
		 * funcion de manejo de archivos subidos.
		 */
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
				$tip = explode("/",$tipo); // es ruta de url.
				// $nombre = $agregado.$v["name"].
				if ($tip[0] == "image" ){
					// cambiando el nombre para que no se sobreescriba con otro archivo
					// accidentalmente.
					$nombre = $agregado.base64_encode( rand(100000,999999) ).".".$tip[1];
					// carpeta debe existir.
					$destino = self::$ruta.DIRECTORY_SEPARATOR.$carpeta.DIRECTORY_SEPARATOR.$nombre;
					if (!self::Existe(self::$ruta.DIRECTORY_SEPARATOR.$carpeta)){
						// no existe carpeta crearla:
						try{
							mkdir(self::$ruta.DIRECTORY_SEPARATOR.$carpeta,0777,true);
						}catch(Exception $e){
							MiControlError::gestorErrores(404, "Falla creacion de ruta", __FILE__, __LINE__, array($destino));
							exit(1);
						}
					}

					// mover el archivo de temporario a final
					try {
						move_uploaded_file("$archivo","$destino");
						$r=true;
					}catch (Exception $e){
						MiControlError::gestorErrores($e->errorNumber, "Falla al momver archivo".$e->errorMessage
							, __FILE__, __LINE__
							, array($destino,$archivo,$e->vars));
						$r=false;
					}
				}
				else{
					// no es un archivo o tuvo alguna falla.
					$r=false;
				}
			}
			$rt[] = array($r,"nombre"=>$nombre_org,"destino"=>$destino,"tipo"=>$tipo,"size"=>$size);
		}
		return $rt;
	}

	public function recuperar($carpeta,$archivo){
		$ruta = self::$ruta.DIRECTORY_SEPARATOR.$carpeta.DIRECTORY_SEPARATOR.$archivo;
		if($this->fileexists($ruta)){
			return file_get_contents($ruta);
		}else{
			return false;
		}
	}
}
