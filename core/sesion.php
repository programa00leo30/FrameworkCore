<?php
class sesion {
	const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;
   
    private $sessionState = self::SESSION_NOT_STARTED;
   
    private static $instance;
   
    
	function __construct() {
		// $this->getInstance();
		// echo "iniciado<br>\n";
	}

	public function set($nombre, $valor) {
		global $_SESSION;
		// echo "guardando valor $nombre<br>\n";
		$_SESSION [$nombre] = $valor;
	}

	public function get($nombre) {
		global $_SESSION;
		// echo "<div>solicitando valor '$nombre'</div><br>\n";
		if (isset ( $_SESSION [$nombre] )) {
			// echo "<div>valor encontrado</div>";
			return $_SESSION [$nombre];
		} else {
			// echo "<div>sin valor</div>";
			return false;
		}
	}
		
	public function borrar_variable($nombre) {
		global $_SESSION;
		unset ( $_SESSION [$nombre] );
	}
	public function borrarsesion() {
		global $_SESSION;
		$_SESSION = array();
		session_destroy ();
	}
	
	 
    /**
    *    Returns THE instance of 'Session'.
    *    The session is automatically initialized if it wasn't.
    *   
    *    @return    object
    **/
   
    public static function getInstance()
    {
        if ( !isset(self::$instance)){
			 self::$instance = new self;
			// echo "inicialiarce<br>\n";
        }
       
        self::$instance->startSession();
       
        return self::$instance;
    }
    
	    /**
    *    (Re)starts the session.
    *   
    *    @return    bool    TRUE if the session has been initialized, else FALSE.
    **/
   
    public function startSession()
    {
		global $_SESSION;
        if ( $this->sessionState == self::SESSION_NOT_STARTED )
        {			
			session_name(SESION_NOMBRE);
			ini_set("session.cookie_lifetime","86400"); // 24 hs.
			ini_set("session.gc_maxlifetime","86400");   // 24 hs.
			// @session_start();
            $this->sessionState = session_start();
            if (isset($_SESSION["msg"])){
				// unico objeto que envia mensaje.
				// echo "<div class=\"sesion_mensaje\">".$_SESSION["msg"]."</div><br>\n";
				// var_dump($_SESSION["msg"]);
				$t=$_SESSION["msg"];
				$_SESSION["msg"]=array($t);
				foreach ($_SESSION["msg"] as $m)
					mensaje($m);
				
				unset($_SESSION["msg"]);
				
			}
        }
       
        return $this->sessionState;
    }
   
      
    public function __isset( $name )
    {
        return isset($_SESSION[$name]);
    }
    
     
    /**
    *    Stores datas in the session.
    *    Example: $instance->foo = 'bar';
    *   
    *    @param    name    Name of the datas.
    *    @param    value    Your datas.
    *    @return    void
    **/
   
    public function __set( $name , $value )
    {
		if ($name == "msg"){
			echo "msg agregado:";
			// acumular mensajes
			$_SESSION["msg1"] = $value;
			$_SESSION["msg"][] = $value;
		}else{
			$_SESSION[$name] = $value;
		}
    }
   
   
    /**
    *    Gets datas from the session.
    *    Example: echo $instance->foo;
    *   
    *    @param    name    Name of the datas to get.
    *    @return    mixed    Datas stored in session.
    **/
   
    public function __get( $name )
    {
        if ( isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }else{
			return null;
		}
			
    }
    
    
    /**
    *    Destroys the current session.
    *   
    *    @return    bool    TRUE is session has been deleted, else FALSE.
    **/
   
    public function destroy()
    {
        if ( $this->sessionState == self::SESSION_STARTED )
        {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );
           
            return !$this->sessionState;
        }
       
        return FALSE;
    }
    
    
}
?>
