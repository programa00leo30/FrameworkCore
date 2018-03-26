		</div>
		<footer>
			<div class="container">
				<!-- <p>Desarrollado por Leandro Morala</p> -->
				<center><p>Desarrollado por Leandro Morala</p><p>PLATAFORMA ESPECIALIZADA</p></center>
			</div>
			<div class="container row" >
			<?php
			if (isset($mensaje)) {
			?>
				<div class="col-lg-12"><?php echo $mensaje ; ?></div>
				<div class="col-lg-12"><?php echo debugf("",1) ; ?></div>
				<button class="btn btn-info col-md-12">boton info</button>
			<?php 
			} 
			
			
			?>
				
			</div>
			
		</footer>
	</body>
</html>
