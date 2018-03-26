<?php
define("CONTROLADOR_DEFECTO", "index");
define("ACCION_DEFECTO", "index");

define("CONTROLADOR_FIN", "fin");
define("ACCION_FIN", "gracias");

define("debugmode",true);

// session_name("iduv");
define("SESION_NOMBRE","iduv");

$ScrtipDire = pathinfo( __file__, PATHINFO_DIRNAME);
$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

define("PATH","/home/u302896513/public_html/iduv/front");
define("URL" ,"//$domain/") ;

//Más constantes de configuración
?>
