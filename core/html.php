<?php

class html implements Iterator {
	private $etiqueta, $atributos,$contenido,$extra;
	protected $SinCierre=["br","meta","link","input","col"];
	protected $tab=0;
	protected $noCerrar=false;
	protected $indice=0;
	protected $maxLen = 105;
	public function __construct($etiqueta,$atributos=array(),$contenido=array(),$atrExtras=""){
		
		$this->etiqueta = $etiqueta ;
		$this->extra = $atrExtras ;
		foreach ($atributos as $k=>$v){
			$this->atributos[$k]=$v;
		}
		if (is_array($contenido)){
			foreach ($contenido as $obj){
				if (is_object($obj))
				if ($obj instanceof html){
					$obj->tab($this->tab+1);
					
				}
			}
		}
		$this->contenido = $contenido;
		
	}
	public function getElemto(){
		return $this->etiqueta;
	}
	
	public function __get($campo){
		if (is_array($this->contenido )){
		foreach ( $this->contenido as $k=>$v)
			if ($v instanceof html)
				if ($v->getElemto() == $campo){
					return $v;
				}
		}else{
			if ($this->contenido instanceof html)
				if ($this->contenido->getElemto() == $campo)
					return $this->contenido;
		}
		return new html("span",[id=>$campo],$campo);
		
	}
	
	public function add($htmlObject){
		if (is_array($this->contenido)){
			if (is_array($htmlObject)){
				// foreach ($htmlObject as $o) $o->tab($this->tab + 1);
				$this->contenido= array_merge($this->contenido,$htmlObject);
			}else{
				$this->contenido[] = $htmlObject;
			}
		}else
		$this->contenido = array($this->contenido , $htmlObject );
		
	}
	public function tab($n){
		$this->tab=$n;
	}
	public function noClose(){
		/* accion extraña por la que no
		 se requiere cierre de etiqueta.
		*/
		$this->noCerrar=true;
		
	}
	public function GetTab(){return $this->tab; }
	
	public function SetAtr($key,$val){
		// remplazar / cambiar atributo
		$this->atributos[$key]=$val;
	}
	public function GetById($id){
		$r=$this->GetAtr("id");
		if ($r){
			if ($r == $id) {return $this;
			}else{
				if (is_array($this->contenido)){
					foreach ($this->contenido as $o)
						if ($o instanceof html){
							if ( $o->GetAtr("id") == $id ) { return $o; break; }
						}
					return new html("-");
				}else{
					if ($this->contenido instanceof html){
						return $o->GetById($id);
					}else{
						return new html("-");
					}
				}
			}
		}else{
			if (is_array($this->contenido)){
				foreach ($this->contenido as $o)
					if ($o instanceof html){
						if( $o->GetAtr("id") == $id) { return $o;break;}
					}
			}else{
				if ($this->contenido instanceof html){
					return $o->GetById($id);
				}else{
					return new html("-");
				}
			}
		}		
	}
	public function GetAtr($key){
		// obtener atributo:
		return (isset($this->atributos[$key])?$this->atributos[$key]:this );
	}
	public function GetAtrAll(){
		return $this->atributos;
	}

	public function SetContent($content){
		$this->contenido = $content;
	}
	public function AddContent($content){
		$this->contenido .= $content;
	}
	
	public function getContent(){
		return $this->contenido;
	}
	public function showAtr(){
		$t="";
		if (is_array($this->atributos))
			foreach ($this->atributos as $k=>$v)$t.=" ". $k.'="'. $v . '"';

		return $t;
		
	}
	public function tabular($contenido){
		if (is_array($contenido)){
			// tabular contenido.
			foreach ($contenido as $k=>$o){
				if ($o instanceof html) $o->tab($this->tab + 1);
				else {
					if (strlen($contendio) > $this->maxLen )
						$contenido[$k] = str_repeat("\t",$this->tab + 1).$o;
				}
			}
			$tcont = implode("\n",$contenido);

			if (strlen($tcont) < $this->maxLen) 
				$tcont = ((count($contenido) > 1)?implode("\n",$contenido):ltrim($contenido[0]));
			
			$contenido = $tcont;
			
		}else 
			if ($contenido instanceof html) {
				if( strlen( (string)$contenido ) > $this->maxLen ){
					$contenido->tab($this->tab + 1 );
					
				}else{
					$contenido->tab(0);				
				}
			}
			else 
				if (strlen($contenido) > $this->maxLen  ) 
					$contenido = str_repeat("\t",$this->tab + 1).$contenido ;
		return (string)$contenido;
	}
	
	private function __conten(){
		/*
		if (is_array($this->contenido)){
			$rt="";
			foreach($this->contenido as $ob){
				if ($ob instanceof html ) $rt.="\n";
				if (strlen($ob)>0) $rt.="\n";
				$rt.=$ob;
			}
			//$rt =  implode("\n",$this->contenido);
		}else{
			// se asume que es un string. o un solo objeto.
			$rt = $this->contenido;
		}
		*/
		
		$rt=$this->tabular($this->contenido);
		if (strlen($rt) > 0 ){
			$rt = ">"
			// ."(".strlen($rt).")"
			. ((strlen($rt) < $this->maxLen)?"":"\n" )
			. $rt .((strlen($rt) < $this->maxLen)?"":"\n" )
			.((strlen($rt) < $this->maxLen)?"":str_repeat("\t",$this->tab )) 
			.(($this->noCerrar)?"":"</". $this->etiqueta .">") ;
		}else{
			if (!$this->noCerrar){
				if ( in_array($this->getElemto(), $this->SinCierre )){
					$rt = "/>" ;
				}else{
					$rt="></". $this->etiqueta .">" ;
				}
			}else{
				$rt=">\n";
			}
		}
		return $rt;
		
	}
	
	public function __toString(){
		if ( PHP_SAPI <> "cli" ) { // ; // devuelve CLI o apache2handler
		return str_repeat("\t",$this->tab)."<". $this->etiqueta  
			. ((strlen($this->extra)>0)?" ".$this->extra:"") 
			. $this->showAtr() 
			. $this->__conten() 
			
			;
		}else
			return $this->toStringConsole();
			
	}
	/* imprimir por consola los datos. */
	public function toStringConsole(){
		$rt="";$contenido=$this->contenido;
		
		if (is_array($contenido)){
			// tabular contenido.
			foreach ($contenido as $k=>$o){
				if ($o instanceof html) $rt.=$o->toStringConsole();
				else {
					$rt.="\n ".$o;
				}
			}
			
		}else 
			if ($contenido instanceof html) {
				$rt.=$contenido->toStringConsole();
			}
			else 
				$rt.= $contenido;
		return (string)$rt."\n";
		
	}
	/******************************
	 * 
	 * funcion especializada en entendimiento 
	 * de etiquetas
	 * pasadas por parametros de texto.
	 * para devolver como objeto.
	 * 
	 * ********************************/
	private function interpretarhtml($texto){
		
		$t= new class  {
			private $param ;
			
			public function __construct(){ $this->param["etiqueta"] = "" ; }
			
			public function set_etiqueta($etiqueta){$this->param["etiqueta"] = $etiqueta ;	}
			public function get_etiqueta($etiqueta){ return $this->param["etiqueta"] ;	}
			public function analiza($entrada,$etiqueta){ 
				$this->param["etiqueta"] = $etiqueta ;	
				return $this->analizarEtiquetasRecursivo($entrada);
			}
			public function etiquetas($entrada){
				$etiquetas = array("html","head","body","div","ul","li","table","tr","td","form");
				foreach($etiquetas as $etq){
				$t = $this->analiza($entrada,$etq);
				if ($t) return $t;
				}
				return false;
			}
			
			function analizarEtiquetasRecursivo($entrada)
			{
				$etiqueta = $this->param["etiqueta"];
				// $regex = '#\[url]((?:[^[]|\[(?!/?url])|(?R))+)\[/url]#';
				$regex = '#\<'.$etiqueta.'>((?:<^[]|\<(?!/?'.$etiqueta.'>)|(?R))+)\</'.$etiqueta.'>#';
				
				if (is_array($entrada)) {
					// $entrada = '<div style="margin-left: 10px">'.$entrada[1].'</div>'."\n";
					$t=explode("/",$entrada[1]);
					// $entrada = "http:".$h->url($t[0],$t[1]);
					// $entrada = '<div style="margin-left: 10px">'.$entrada[1].'</div>'."\n";
				}
				$obj = new html($etiqueta
					,$atr
					,preg_replace_callback($regex, 'self::analizarEtiquetasRecursivo', $entrada)
					);
				// return preg_replace_callback($regex, 'self::analizarEtiquetasRecursivo', $entrada);
				return $obj ;
			}
			
		} ;
		// $t=new recursivo();
		return $t->etiquetas($texto);
		
	}

	/* *******************************
	 * 
	 * funciones especificas de iteraciones
	 * para poder recorrer el objeto 
	 * completo
	 * 
	 * ********************************/

	public function rewind()
    {
        /*
        echo "rebobinando\n";
        reset($this->var);
		*/
		$this->indice=0;
    }

    public function current()
    {
		// return current($this->contenido[$this->indice]->GetElemto() );
		return $this->contenido[$this->indice];

    }

    public function key()
    {
		/*
        $var = key($this->var);
        echo "clave: $var\n";
        return $var;
        */
        return $this->contenido[$this->indice]->GetElemto();
    }

    public function next()
    {
		$this->indice++;
		if (is_array($this->contenido))
			if (isset($this->contenido[$this->indice]))
				return $this->contenido[$this->indice];
			else
				return false;
		else
			return false;

    }

    public function valid()
    {
		/*
		 * validar la posicion actual del puntero
        $clave = key($this->var);
        $var = ($clave !== NULL && $clave !== FALSE);
        echo "válido: $var\n";
        return $var;
        */
        $rt=false;
        if (is_array($this->contenido)){
			if (isset($this->contenido[$this->indice ])){
				$rt=true;
			}
		}
		return $rt;
    }

}

class coment extends html{
	private $txt="";
	private $idin;
	static private $id=0;
	
	public function __construct($comentario){
		// comentario es un texto.
		$this->txt = $comentario;
		$this->idin = "coment_".(self::$id++);
		
	}
	public function GetById($id){
		if ( "coment_$id" == $this->idin)
			return $this;
		else
			return false;
	}
	public function add($object){
		// no se puede agregar un objeto a un comentario.
		$this->txt .= (string) $object ;
	}
	public function tab($n){
		$this->tab=$n;
	}
	public function __toString(){
		
		return  ((strlen($rt) < $this->maxLen)?"":"\n" )
				.str_repeat("\t",$this->tab )
				."<!--"
				."(".$this->idin.")"
				.$this->txt
				."-->";
	}
}
