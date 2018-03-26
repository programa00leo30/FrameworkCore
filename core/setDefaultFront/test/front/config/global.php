<?php
define("CONTROLADOR_DEFECTO", "index");
define("ACCION_DEFECTO", "index");

define("CONTROLADOR_FIN", "fin"); // define pagina de salida
define("ACCION_FIN", "gracias");   // define pagina especial de salutacion

define("debugmode",true);		// define produccion o desarrollo ( debugmode=true => desarrollo )

// session_name("iduv");
define("SESION_NOMBRE","Test");  // nombre a la cookie de sesion:

$ScrtipDire = pathinfo( __file__, PATHINFO_DIRNAME);	// directorio padre ( actual. )
$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // host de origen de la peticion

// define("PATH","/home/u302896513/public_html/iduv/front"); // path real al cual dirigirse no como url.
define("PATH","$actualDir$objetivo"); // declarado en el index!!.
define("URL" ,"//$domain/") ;		// url destino ( exactamente como aparece en el url del sitio )

//Más constantes de configuración
?>
