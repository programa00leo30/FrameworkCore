<?php

/* Example for adding a VPN user */

require('../routeros_api.class.php');

$API = new RouterosAPI();

$API->debug = true;

if ($API->connect('10.255.255.20', 'systema', 'none')) {

   $API->comm("/ppp/secret/add", array(
      "name"     => "user",
      "password" => "pass",
      "remote-address" => "172.16.1.10",
      "comment"  => "{new VPN user}",
      "service"  => "pptp",
   ));

   $API->disconnect();

}

?>
