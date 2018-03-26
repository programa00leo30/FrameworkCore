<?php

	/*
	en caso de que llamen a una funcion desconocida del controlador 
	*/
$url=URL."index.php/";
$Pagtitulo="PAGINA NO ENCONTRADA" ;
include("head.php");
include("estructura.php");
?>
			<div class="container-fluid">
				<h2>ERROR 404 </h2><p><strong><?php  echo $title ?></strong>- no se ha encontrado <strong><?php  
				echo $name ?></strong></p>
				<div><a href="<?php echo $url ?>" >regresar al inicio</a></div>
			</div>
		
	<?php 
include("footer.php") ; 
?>
	
