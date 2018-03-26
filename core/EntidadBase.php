<?php
class EntidadBase{
    protected $table;
    protected $db;
    protected $conectar;
    protected $idRecordset; 	// para saber en que id estoy.
	protected $columnas ;
	protected $ordeBy ;		// establecer el orden.
	protected $where ;		// establecer condicion para toda la tabla.

    public function __construct($table,$perfil="default") {
        $this->table=(string) $table;
         
        require_once 'Conectar.php';
       
        $this->conectar=new Conectar($perfil);
        $this->db=$this->conectar->conexion();
		// $this->columnas = array(); // inicializacion de campos.
		if (! isset($this->where )) { $this->where="" ;} ;
	}
	
	public function error($rtnQuery,$strQuery){
		// controlador de errores de la base de datos.
		/*
		var_dump($this->conectar);
		echo "-------------------- esta pasando por aqui --------------" ;
		var_dump($rtnQuery);
		*/
		if ( !$rtnQuery  ){
			// el retorno es falso, falla de consulta.
			// sesion::set("msg","(".$this->con->errorCode.")".$this->con->errorInfo ."query:$strQuery" );
			trigger_error("(".$this->conectar->errorCode.")".$this->conectar->errorInfo ."consulta:$strQuery" , E_USER_ERROR);
			// crear un registro en blanco.
			$resultSet = new objeto ;
			foreach ( $this->columnas as $camop ) 
				$resultSet->$camop = "";
			
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
			return $rtnQuery ;
		}
	}
	
    public function columnas (){
		return $this->columnas ;
	}
	/*
    protected function error(){
		// devuelve un mensaje con el error
		if ($this->errorCode){
			return "entidad: (".$this->errorCode.")".$this->db()->error ;
		}else{
			return false; // sin error.
		}
	}
	*/
	
    public function getConetar(){
        return $this->conectar;
    }
     
    public function query($query){
		// salvedad para utilizar con precacucion.
        return $this->db->query($query);
    }
     
    public function db(){
        return $this->db;
    }
    
	
			
    public function getAll($orden = "default" ,$inic=0,$cant=0 ){
		if ($orden = "default" ){ $orden = $this->ordeby ; }
		
		// echo "sql:"."SELECT * FROM $this->table ORDER BY id DESC" ;
		if ($cant != 0 ){
			$limit = " LIMIT $inic , $cant ;";
		}else
			$limit ="";
		$strQuery="SELECT * FROM $this->table $orden ".$this->where ." $limit" ;
		// echo "<p>$strQuery</p>";
        $query=$this->db->query($strQuery);
        // echo "SELECT * FROM $this->table $orden $limit" ;
        $resultSet = array();
        //Devolvemos el resultset en forma de array de objetos
        while ($row = $query->fetch_object()) {
           $resultSet[]=$row;
        }
         
        return $resultSet;
    }
     
    public function getById($id){
		
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

    public function getBy($column,$value){
        $sql= "SELECT * FROM $this->table WHERE $column='$value' ;";
        // $sql= "SELECT * FROM $this->table WHERE idMesa='5' ;";
        // echo $sql;
        $query=$this->db->query($sql);
		if ($query){
			if ($query->num_rows > 0 ){
				while($row = $query->fetch_object()) {
				   $resultSet[]=$row;
				}
			}else{
				$resultSet = array();
			}
		}else{
			// falla de consulta
			echo "falla de sistema . ";
			echo $this->db()->error;
			exit ;
		}
		// echo "error ";
				
        return $resultSet;
    }
     
    public function deleteById($id){
		$sql="DELETE FROM ".$this->table." WHERE id='$id' limit 1, 1 ;";
		// echo $sql."\n<br>";
        // ejecutando la consulta.
        $query=$this->db->query($sql); 
        if (!$query){
			// falla de consulta
			echo "falla de sistema . ";
			echo $this->db()->error;
			exit ;
		}
        return $query;
        
    }
     
    public function deleteBy($column,$value){
        $query=$this->db->query("DELETE FROM $this->table WHERE $column='$value'"); 
        return $query;
    }
     
 
    /*
     * Aquí podemos montarnos un montón de métodos que nos ayuden
     * a hacer operaciones con la base de datos de la entidad
     */
	public function buscar($campo,$valor,$especial=''){
		$cmd="SELECT * FROM $this->table ".$this->where." ORDER BY id DESC";
		$query=$this->db->query($cmd);
        $query= $this->error($query,$cmd);
        
		
        //Devolvemos el resultset en forma de array de objetos
        while ( $row = $query->fetch_object() ) {
			// var_dump($row);
			if (!isset($row->$campo)){
				// echo "no encontrado falla campo";
				return false; // no existe el campo.
			}else{
				
				if ($row->$campo == $valor){
					// echo "encontrado :$campo \"$valor\" ";
					 foreach($row as $k=>$v){
						// esto asigna los valores encontrados al entorno general.
						$this->$k = $v;
						// echo "asignado $k = $v <br>\n";
					}
					// $this->$row; // valores para todos los campos.
					return $row ; // valor encontrado
				}else{
					// echo "\"" . $row->$campo."\" != \"$valor\"<br>\n" ;
				}
				
			}
		}
		
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
	
}
