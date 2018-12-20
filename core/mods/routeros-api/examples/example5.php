<?php

/* Example of counting leases from a specific IP Pool (using regexp) */

require('../routeros_api.class.php');

$API = new RouterosAPI();

$API->debug = true;

if ($API->connect('10.255.255.20', 'systema', '4789q4123')) {

   $ARRAY = $API->comm("/ip/dhcp-server/lease/print", array(
      // "count-only"=> "",
      "?dynamic"=> "yes",
      // "~active-address" => "172.20.",
      // "~active-address" => "1.1",
   ));
   
	echo "----\n";
   print_r($ARRAY);
	echo "\n----";
	foreach ($ARRAY as $n=>$li){
		$rt1=$API->comm("/ip/dhcp-server/lease/make-static",array(
			"numbers"=>$li[".id"],
			)
		);
		print_r($rt1);
		$rt2=$API->comm("/ip/dhcp-server/lease/set",array(
			"numbers"=>$li[".id"],
			"address-lists" => "cliente",
			"comment" => "agregado automaticamente",
			)
		);
		print_r($rt2);
		$perfil["target"] = $li["active-address"];
				
		$rt3= $this->api->comm("/queue/simple/add",$pefil);
		print_r($rt3);
	}
	
   $API->disconnect();

}

?>
