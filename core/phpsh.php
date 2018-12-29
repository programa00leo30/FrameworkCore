<?php


function execute($cmd) { 
	$proc = proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes); 
	while(($line = fgets($pipes[1])) !== false) { 
		// fwrite(STDOUT,$line); 
	} 
	while(($line = fgets($pipes[2])) !== false) { 
		// fwrite(STDERR,$line); 
	} 
	fclose($pipes[0]); 
	fclose($pipes[1]); 
	fclose($pipes[2]); 
	return proc_close($proc); 
}




	$t="";
	$fp = fopen("php://stdin", "r");
	$in = '';
	$prnt="PHP>";
	while($in != "quit") {
		// echo "php ". __FILE__ ."> ";
		echo $prnt;
		// $in=trim("globals \$htm;\n".fgets($fp));
		$in=trim(fgets($fp));
		
		$shell = " echo -e '<?php $in'" . ' | php -l  '; // &>/dev/null ; if [ $? -eq 0 ]; then echo "si" ; else echo "no"; fi ';
		// $rt = shell_exec($shell);
		$rt=execute($shell);
		if ( $rt == 255 )	{ $t .= $in ; echo "\n"; $prnt="PHP+:" ; }
		else {
			$t .= $in;
		
			try { 
				eval ($t); 
			}
			catch ( Exception $e ){
				echo 'Excepción capturada: ',  $e->getMessage(), "\n";
			}
			$t="";$prnt="PHP>" ;
		}
	}
