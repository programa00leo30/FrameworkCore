<?php

class EntidadBase extends ExtensionPuente{
    protected $table;
    protected $db;
    protected $conectar;
    protected $idRecordset; 	// para saber en que id estoy.
    protected $selected = false; 	// para saber si hay un registro elegido.
	protected $columnas ;		// listado de las columnas de la tabla
	protected $atributos ;		// atributos de cada columna de la tabla
	protected $ordeBy ;		// establecer el orden.
	protected $where ;		// establecer condicion para toda la tabla.
	protected $falla = false; // sin fallas en la consulta.
	
	public $paginn; 		// un paginador.
	public $objetos ;		// los distintos objetos de las paginas.
	
	
    public function __construct($table,$perfil="default") {
		parent::addExt(new EntidadBaseFormularios());
        $this->table=(string) $table;
        $this->ordeBy = ""; // sin orden por defecto
        $this->where ="";
		
        
        foreach($this->atributos as $k=>$v) {
			// $this->$k = new objeto($this->atributos[$k]);
			// $this->$k = "";
			if ( ( $k != "where" ) && ( $k != "orderBy" ) ){ // atributos especiales.
				$this->columnas[]=$k;
			}
		}
       
        $this->conectar=new Conectar($perfil);
        
		$this->db=$this->conectar->conexion();
		// $this->columnas = array(); // inicializacion de campos.
		if (! isset($this->where )) { $this->where="" ;} ;
		
		// $this->crearObjetos();
		
	}
	public function selecionado(){
		return $this->idRecordset();
	}
	public function idRecordset(){
		// ultimo valor adquirido del recodset actual
		// si no hay seleccionado uno devolvera null.
		if (is_null($this->idRecordset)){
			return false;
		}else{
			return $this->idRecordset;
		}
	}
	public function setWhere($condicion){
		// condicional sin filtro..
		$this->where = $condicion;
		
	 }
	private function where(){
		if ($this->where != "") $rt = "WHERE ".$this->where; 
		else $rt="";
		 
		return $rt;
	}
	public function popiedades($nombreColumna,$propiedades){
		foreach($propiedades as $k=>$v){
			$this->objetos->$k = $v;
		}
	}

	public function columns(){
		return $this->columnas ;
	}
		
    public function columnas (){ 
		return $this->columnas ;
	}
	public function error($rtnQuery,$strQuery){
		
		if ( !$rtnQuery  ){
			// el retorno es falso, falla de consulta.
			// sesion::set("msg","(".$this->con->errorCode.")".$this->con->errorInfo ."query:$strQuery" );
			trigger_error("(".$this->conectar->errorCode.")".$this->conectar->errorInfo 
				."consulta:$strQuery" , E_USER_ERROR);
			// crear un registro en blanco.
			$resultSet = new objeto ;
			foreach ( $this->columnas as $camop ) 
				$resultSet->$camop = "";
			$this->falla=true;
			
			return $resultSet ;
		}
		else{
			// la consulta esta correcta pero puede haber 0 resultados.
			// $rtnQuery->num_rows == 0 // significa que no hay resultados.
			if ($rtnQuery->num_rows == 0 ) {
				$rtnQuery = new objeto ;
				foreach ( $this->columnas as $camop ) 
					$rtnQuery->$camop = "";
			}
			$this->falla = true;
			
			return $rtnQuery ;
		}
	}

    public function getConetar(){
        return $this->conectar;
    }
     
    public function query($query){
		// salvedad para utilizar con precacucion.
        try{
			$rt =  $this->db->query($query);
			return $rt;
		}
		catch (Exception $e) 
		{
			Debuger::warn("falla sql: $query ".$e->getMessage() );
			return false;
		}
		
    }
     
    public function db(){
        return $this->db;
    }
    
	public function fail(){
		// retorna si fue exitosa la consulata
		return $this->falla;
	}
			
    public function getAll($orden = "default" ,$inic=0,$cant=0 ){
		
		if ($orden == "default" ){ $orden = $this->ordeBy ; }
		
		// echo "sql:"."SELECT * FROM $this->table ORDER BY id DESC" ;
		if ($cant != 0 ){
			$limit = " LIMIT $inic , $cant ;";
		}else
			$limit ="";
		$strQuery="SELECT * FROM ".$this->table." ".$this->where() ." $orden $limit" ;
		// echo "<p>$strQuery</p>";
        $query=$this->query($strQuery);
        // echo "SELECT * FROM $this->table $orden $limit" ;
        $resultSet = array();
        //Devolvemos el resultset en forma de array de objetos
        while ($row = $query->fetch_object()) {
           $resultSet[]=$row; //new html("p",[],$row);
        }
		$this->idRecordset = null;
        $this->paginn = count($resultSet);
        return $resultSet;
    }
	// private function _tabular($indent){ return $tabulador="\n".str_repeat("\t",4+$indent);}
    // html es una herramienta eredada del momento de renderizado.
    public function mostrar_editar($campo,$html=null,$valor=null){
		// funcion que devuelve un contenido html
		// para la edicion del campo.
		//require_once("EntidadBaseFormularios.php");
		//$for=new EntidadBaseFormularios();
		
		$txt="";
		// $tabulador="\n".str_repeat("\t",4);
		if (in_array($campo,$this->columnas)){
			// el campo existe:
			$atr = $this->atributos[$campo] ;
			$extra=nz($this->atributos[$campo]["extras"],[]);
			if (!isset($valor)){
				if ($this->$campo != "NULL" ){
				// $valor = ( $this->$campo != "NULL" )?$this->$campo:"";
					$valor = $this->$campo;
						
				}else{
					if (isset( $this->atributos[$campo]["dbdefault"])){
						// valor por defecto.
						if ( $this->atributos[$campo]["dbdefault"] == "timestamp" ){
							// fecha / hora 
							$valor = date("Y-m-d H:i:s") ;
						}elseif ( $this->atributos[$campo]["dbdefault"] == "date" ){
							$valor = date("Y-m-d") ;
							
						}else{
							
							$valor = $this->atributos[$campo]["dbdefault"] ;
						}	
					}else{
						$valor=""; // sin valor.
						
					}
						
				}
			}
				
			if ($atr["dbtipo"] == "not null"){
				$extra["required"]="required" ;
			}
			$placeholder = isset($atr["comenta"])?$atr["comenta"]:$campo ;
			$label=isset($atr["label"])?$atr["label"]:$placeholder;
			$lista=array();
			if (isset($atr["list"])){
				// arreglo de valores a listar en un desplegable.
				foreach( $atr["list"] as $v)$lista[$v]=$v;
			}
			if (isset($atr["sql"])){
				$ls = $this->query($atr["sql"][1]);
				
				if ( is_null($ls) ){
					// echo "null: ".$atr["sql"][1]."\n";
				}else{
					while ($row = $ls->fetch_object()) {
						$lista[$row->{$atr["sql"][0]}]=$row->{$atr["sql"][2]};
					}
				}
			}
			
			return $this->{$atr["typeform"]}($campo,$valor,$placeholder,$extra,$lista);
			/*
			$txt.=$this->{$atr["typeform"]}($campo,$valor,$this->_tabular(2),$placeholder,$extra,$lista);
			if (isset($atr["htmlfirst"])){
				$txt= $this->_tabular(1).$atr["htmlfirst"].$this->_tabular(0).$txt ;
			}
			if ( isset($atr["clas"] )){
				$txt = $this->_tabular(2)."<span class=\"".
					$atr["clas"]."\">$label</span> $txt" ;
			}
			
			if (isset($atr["htmllast"])){
				$txt="$txt ".$this->_tabular(1).$atr["htmllast"]."\n" ;
			}
			$txt = $this->_tabular(0)."<div class=\"input-group\" >$txt".$this->_tabular(0)."</div>";
			return $txt;
			*/
		}else{
			return new html("div",[],"->$campo<-");
		}
	}
	public function mostrar($campo,$valor,$relacion="=",$extra="") {
		// limita a una condicion simple de consulta
		static $bandera=true; // iteraccion de inicio.
		static $query;
		static $posicion=0;
		if ($bandera){
			// echo "entrando $posicion";
			// primera vuelta verificar estado y comenzar.
			$sql= "SELECT * FROM $this->table WHERE `$campo`$relacion'$valor' $extra ;";
			// echo $sql;
			$query=$this->query($sql);
			if (!$query){
				// falla de consulta.
				// falla de consulta
				echo "falla de sistema . ";
				echo $this->db()->error;
				$this->idRecordset=null;
				echo $sql;
				exit ;
			};
			/*
			echo "<!-- inicio de registros: $sql \n" ;
				var_dump($query);
			echo "-->\n" ;
			*/
			$posicion=0;
			$bandera=false;
		}
		// cargando la primera vuelta de objetos:
		 //Devolvemos el resultset en forma de array de objetos
		$move= $query->data_seek($posicion);
			
		if ($move){
			$posicion++;
			$row = $query->fetch_object()  ;
			{
				// var_dump($row);
				if (!isset($row->$campo)){
					echo "<!--- no existe el campo -->";
					return false; // no existe el campo.
				}else{
						
					// if ($row->$campo == $valor) // segunda comprobacion de veracidad de la consulta..
						{
						// echo "encontrado :$campo \"$valor\" ";
						 foreach($row as $k=>$v){
							// esto asigna los valores encontrados al entorno general.
							if (isset($this->atributos[$k])){
								$this->$k = $v;
								if (isset($this->atributos[$k]["dbtipo"]) and ($this->atributos[$k]["dbtipo"]=="autoincrement")){
									// clave id:
									$this->idRecordset = $v ;
								}
							}
							// echo "asignado $k = $v <br>\n";
						}
						// $this->$row; // valores para todos los campos.
						return $row ; // valor encontrado
					}
					/* else{
						echo "\"" . $row->$campo."\" != \"$valor\"<br>\n" ;
					} */
						
				}
			}
		}else{
			$bandera=true; // reiniciar el funcionamiento.
			// echo "<!-- fin de registros $sql -->\n" ;
			return false;
		}	
	}


    public function checkForm($post){
		$chk=true;$fail=array();
		DebugerCore::log("control_EntidadBase","verificando campos");
		 
		foreach($this->columnas as $campo){
			if (array_key_exists($campo,$post)){
				// buen camino.
				DebugerCore::log("Control_EntidadBase","el campo $campo es verificado!");
				$this->$campo = $post[$campo];
			}else{
				// mal caminio. 
				// verificar si es necesario. ( null )
				if ( $this->atributos[$campo]["dbtipo"] == "not null" )
				{
					// el id es de tipo autoincrement. ( unico de su tipo. )
					DebugerCore::log("Control_EntidadBase","el campo $campo es nulo falla!");
					$chk=false; // falla de comprobacion.
					$fail[]=$campo;
				};
				if ($this->atributos[$campo]["dbtipo"] == "default"){
					// tiene valor por defecto.
					DebugerCore::log("Control_EntidadBase","el campo $campo es nulo valor defecto!");
					$this->$campo  = $this->atributos[$campo]["dbdefault"]
;				}
				
			}
		}
		return array($chk,$fail);
	}
	
    public function guardarform($post,$add=false){
		// obtener los datos de $_POST, verificar y gardar
		$checkStatus=true;
		$checkSalida=array(true,);
		$idSalida=-1;
		
		list($checkStatus,$fail)=$this->checkForm($post);
		if ($checkStatus and $add ){
			// agregar. nuevo
			// echo "agregando\n";
			$idSalida=$this->add();
			// var_dump( $idSalida);echo "\n---------\n";
			
		}elseif ( $checkStatus ){
			// editar existente.
			if (isset($this->id) and ($this->id != "")){
				$idSalida=$this->save();
			}
		}else{
			// falla por algo:
			$checkSalida=array($checkStatus,$fail);
		}
		// var_dump(array($checer,$idSalida));
		return array($checkSalida,$idSalida);	
	}
	
    public function save(){
		// funcion que guarda todo el registro:
		// echo "guardando....";
		$id = $this->id;
		// echo "id=$id<br>\n";
		foreach( $this->columnas as $col){
			// echo "<div>$col valor: ".$this->$col." ($id) </div>\n";
			$this->setById($col,$this->$col,$id);
		}
		// retorno el id al que he guardado.
		return $id;
	}
	
	public function getById($id){
		tiempo( __FILE__ , __LINE__);
        $query=$this->db->query("SELECT * FROM $this->table WHERE id=$id");
		// var_dump($query);
		if (!$query ){ // query es false ( error o no hay registros. )
			$query=$this->db->query("SELECT * FROM $this->table LIMIT 1; ");
			// POR FALLA devuelvo el 1ª objeto obtenido.
		}

		if($row = $query->fetch_object()) {
		   $resultSet=$row;
		}else {
			// crear un registro en blanco.
			$resultSet = new objeto ;
			foreach ( $this->columnas as $camop ) 
				$resultSet->$camop = "";
		}	
		return $resultSet;
		
    }
    
    public function setById($comlumn,$value,$id){
		// UPDATE 'c0740032_algo'.'ciudad' SET 'habitantes' = '15030' WHERE 'ciudad'.'id' =13;
        
			// echo "---------;".$this->{$this->table}["id"].";-----"; 
			// var_dump($this);
        if (strlen($id)>0 ) {
			$sql="UPDATE $this->table SET `$comlumn` = '$value' WHERE id=$id LIMIT 1 ;";
			 $query=$this->db->query($sql );
			// vardump($this->db());
			// debugf( "<div>::$sql::(EntidadBase:54)</div>" ) ;
			
			if($query) {
			   $resultSet=true; // se actualizo
			   // debugf("EntidadBase: (true)-".$this->table."->$sql<--ok<br>\n");
			   // debugf("(ok)<br>\n");
			}else{
				$resultSet=false; // fallo el campo.
				debugf("EntidadBase:setById (false)-".$this->db->error."<br>\n");
			 }
			return $resultSet;
		}else
			return false;
    }
	
	static public function sqlpassowrd($clave){
		// equivalente a sql: SELECT PASSWORD('$clave') ;
		return "*" . strtoupper(
            sha1( sha1($clave, true) )
			);
	}
	
		
    public function getBy($column,$value){
        $sql= "SELECT * FROM $this->table WHERE $column='$value' ;";
        // $sql= "SELECT * FROM $this->table WHERE idMesa='5' ;";
        // echo $sql;
        $query=$this->query($sql);
		if ($query){
			if ($query->num_rows > 0 ){
				while($row = $query->fetch_object()) {
				   $resultSet[]=$row;
				   if ($this->atributos[$k]["dbtipo"]=="autoincrement"){
						// clave id:
						$this->idRecordset = $v ;
					}
				}
			}else{
				$resultSet = array();
			}
		}else{
			// falla de consulta
			// echo "falla de sistema . ";
			// echo $this->db()->error;
			$this->idRecordset=null;
			exit ;
		}
		// echo "error ";
				
        return $resultSet;
    }
     
    public function deleteById($id){
		$sql="DELETE FROM ".$this->table." WHERE id='$id' LIMIT 1 ;";
		// echo $sql."\n<br>";
        // ejecutando la consulta.
        $query=$this->query($sql);
 
		$this->idRecordset=null;
        return $query;
        
    }
     
    public function deleteBy($column,$value){
        $query=$this->query("DELETE FROM ".$this->table." WHERE `$column`='$value'"); 
        $this->idRecordset=null;
        return $query;
        
    }
     
 
    /*
     * Aquí podemos montarnos un montón de métodos que nos ayuden
     * a hacer operaciones con la base de datos de la entidad
     */
	public function buscar($campo,$valor,$especial=''){
		
		$where =$this->where();
		
		$cmd="SELECT * FROM $this->table $where ORDER BY id DESC";
		$query=$this->query($cmd);
        $query= $this->error($query,$cmd);
        $rt = false;
		// echo $cmd;
        //Devolvemos el resultset en forma de array de objetos
        while ( $row = $query->fetch_object() ) {
			// var_dump($row);
			// if (!isset($row->$campo)){
			if (!isset($this->atributos[$campo])){
				return false; // no existe el campo.
			}else{
				
				if ($row->$campo == $valor){
					// echo "encontrado :$campo \"$valor\" ";
					 foreach($row as $k=>$v){
						// esto asigna los valores encontrados al entorno general.
						if (isset($this->atributos[$k])){
							// si el campo no esta definido no se utiliza.
							$this->$k = $v;
							
							if (isset($this->atributos[$k]["dbtipo"]) && $this->atributos[$k]["dbtipo"]=="autoincrement"){
								// clave id:
								$this->idRecordset = $v ;
							}
						}
						// echo "asignado $k = $v <br>\n";
					}
					// $this->$row; // valores para todos los campos.
					return $row ; // valor encontrado
				}else{
					// echo "\"" . $row->$campo."\" != \"$valor\"<br>\n" ;
					// return false;
				}
				
			}
		}
		return false ;
	}
	
	// para que funcion debe escribir estas funciones
	// en cada entidad .
	// ya que en este ambiente no existen los valores de campo.
	
	public function __set($campo,$valor){
		if(in_array($campo,$this->columnas)){
			// $this->columnas[$campo] ;
			$this->$campo = $valor ;
			return $valor;
		}
		return false;
	}	
    public function __get($campo){
		if ( in_array($campo,$this->columnas)){
			if (isset($this->$campo)){
				$t=$this->$campo;
				if ($t="") $t="NULL"; // devolver el TEXTO NULL
				// echo "campo obtenido:$t<br>\n";
				return $this->$campo;
			}else{
				debugf("entidadBase:__get no hay valor asignado para $campo");
				// echo "valor $campo :";var_dump($this->$campo);
				// echo "campo no seteado (val invalido) $campo\n";
				return "NULL"; // no hay informacion 
			}
		}else
			return false;
	}
	
	public function SetOrderBy($intruccion ) {
		// forma de ordenamiento.
		$this->ordeBy = $intruccion ;
	}
	public function GetOrderBy(){
		// forma actual del ordenamiento
		return $this->orderBy ;
	}	
		
	public function limpiarCampos(){
		// antes de todo todos los valores a null
		foreach ($this->columnas as $k=>$v){
			$this->$v = '';
		}
		
	}
		
	public function count(){
		// echo "sql:"."SELECT * FROM $this->table ORDER BY id DESC" ;
					
        $query=$this->db->query("SELECT count(*) as cantidad FROM $this->table ");
        // echo "SELECT * FROM $this->table $orden $limit" ;
         $resultSet = array();
        //Devolvemos el resultset en forma de array de objetos
        while ($row = $query->fetch_object()) {
           $resultSet[]=$row;
        }
        // respondo la cantidad total de registro de la tabla.
        return $resultSet[0]->cantidad ;
    }
    
	public function contar($orden = "default" ,$cant=0,$inic=0 ){
		if ($orden = "default" ){ $orden = $this->ordeby ; }
		
		// echo "sql:"."SELECT * FROM $this->table ORDER BY id DESC" ;
		if ($cant != 0 ){
			$limit = " LIMIT $inic , $cant ;";
		}else
			$limit ="";
		$cmd="SELECT count(*) as contar FROM ".$this->table ." " . $orden ." ". $this->where ." ". $limit;
		
        $query=$this->db->query($cmd);
        // echo $cmd ;
        // echo "SELECT * FROM $this->table $orden $limit" ;
        if ($query) { 
         $resultSet = $query->fetch_object();
        } else {
			$this->error($query,$cmd);
			// echo "falla de sistema . ";
			// echo $this->db()->error;
			// exit ;
		}
        // var_dump($resultSet->contar );
        return $resultSet->contar ;
    }
	/*
	 * AGREGAR NUEVO REGISTRO. 
	 */
	public function add(){
		// agregar un registro nuevo.
		$t="" ;
       //var_dump($this);
		
		foreach($this->columnas as $campo ) {
			if ($campo != "id") {
				if ($this->$campo == '' or $this->$campo == "NULL" ){
					if (isset($this->atributos[$campo]["dbdefault"])){
						
						$t .= " '".$this->atributos[$campo]["dbdefault"]."' ," ;
					}else{
						$t .= " NULL ," ;
					}
				}else
					$t .=  "'".$this->$campo."'," ;
			}
		}
		// quitando la ultima coma.
		$t=substr($t,0,strlen($t)-1);
		
        $query="INSERT INTO ".$this->table." (".implode(", ",$this->columnas).")
                VALUES(NULL, $t );";
        // echo new html("t",['class'=>"hidden",style=>"visible:false;"],"entidad_base:$query");
        $idsave=false;
        $save=$this->query($query);
        if ($save){
			$idsave = $this->db()->insert_id;
			$this->falla = false;
			$this->idRecordset = $idsave;
		
		}else{
		
			$this->falla = true;
			DebugerCore::warn("Control_EntidadBase", $this->db()->error);
			// $t= new html("div",['class'=>"debuger"],"Control_EntidadBase".$this->db()->error);
			// echo $t;
			$save=$this->db()->error;
			$this->idRecordset=null;
		}
		
        return $idsave;
    }
 
}
