<?php
class Conectar{
    private $driver;
    private $host, $user, $pass, $database, $charset;
    protected $con;
    
    public function __construct($name = "default" ) {
		// si existe un nombre al perfil de coneccion usarlo:
        $db_cfg = require PATH.'/config/database.php';
        $this->driver=$db_cfg[$name]["driver"];
        $this->host=$db_cfg[$name]["host"];
        $this->user=$db_cfg[$name]["user"];
        $this->pass=$db_cfg[$name]["pass"];
        $this->database=$db_cfg[$name]["database"];
        $this->charset=$db_cfg[$name]["charset"];
        
    }
     
    public function conexion(){
         
        if($this->driver=="mysql" || $this->driver==null){
            $con=new mysqli($this->host, $this->user, $this->pass, $this->database);
            $con->query("SET NAMES '".$this->charset."'");
        }
        $this->con = $con; 
        return $con;
    }
    
    public function error(){
		// controlador de errores de la base de datos.
		if ($this->con->error){
			// menejador de error
			sesion::set("msg","(".$this->con->errorCode.")".$this->con->errorInfo);
		}
		
	}
  
}
?>
