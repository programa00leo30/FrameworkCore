<?php

if(isset($_GET["ms"])){
	// un mensaje. del sistema:
	$msg = "<div class=\"col-md-3\">".$_GET["ms"]."</div>";
	
}else
	$msg="";
	

include("head.php") ;
?>
    <body>
			<!-- pagina lateral-->
			<div class="col-md-10" id="paginaCentro" >
				<?php echo $msg ?>
				<section class="main row" >
				
					<!-- seccion de informacion -->
				
				</section>
			</div><!-- pagina centro -->
		
		
	<?php 
include("footer.php") ; 
?>
	
