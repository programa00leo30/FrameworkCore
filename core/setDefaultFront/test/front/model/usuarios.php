<?php
class Usuarios extends EntidadBase{
    protected $id;
    protected $nombre;
    protected $apellido;
    protected $password;
    protected $dni;
    protected $calle;
    protected $numero;
    protected $ciudad;
    protected $cp;
    protected $mail;
    protected $sucursal;
    
    // protected $campos;
    /*
		
	*/
	
    public function __construct() {
        $this->columnas  = array("id",  "nombre", "apellido", "password", "dni", "calle", "numero", "ciudad", "cp", "mail", "sucursal"	);
        
        $table="usuarios";
        
        parent::__construct($table);
    }
    
    public function tabla(){
		return $this->table ;
	}
    // generando un getter especial
    public function ciudad() {
		$ciudad = new EntidadBase("ciudad") ;
		if ( $ciudad->buscar("id",$this->ciudad ) ){
			return $ciudad->nombre ."(".$this->ciudad.")";
		} else {
			return "desconocida:(".$this->ciudad.")";
		}
	}
    
	public function check($campo,$valor){
		// busca en la base de datos el valor del campo
		if (in_array($campo,$this->columnas)){
			
			$tmp = parent::getAll();
			foreach($tmp as $tm ){
				if ($tm->$campo == $valor ){
					// valor encontrado.
					foreach($this->columnas as $c){
						$this->$c = $tm->{$c} ;
					}
					return true;
				}
			}
		}else
			return false;
	}
	
    public function add(){
		// agregar un registro nuevo.
		$t="" ;
		
		
		foreach($this->columnas as $campo ) {
			if ($campo != "id") {
				if ($this->$campo == '') 
					$t .= " NULL ," ;
				else
					$t .=  "'".$this->$campo."'," ;
			}
		}
		$t=substr($t,0,strlen($t)-1);
		
        $query="INSERT INTO ".$this->table." (".implode(", ",$this->columnas).")
                VALUES(NULL, $t );";
                       
        $save=$this->db()->query($query);
        if ($save){
			$save = $this->db()->insert_id;
		}
		else{
			echo $this->db()->error;
        }
        
        return $save;
    }
 
}
?>
