<?php

class archivoController extends ControladorBase{
     
    public function __construct() {
        parent::__construct();
    }
    
    public function guardar(){


		$dir_subida = PATH ;
		$newprename=
		$fichero_subido = $dir_subida . basename($_FILES['fichero_csv']['name']);
		
		$mc="--subiendo fichero--<br>\n";

		if (move_uploaded_file($_FILES['fichero_csv']['tmp_name'], $fichero_subido)) {
			$mc .= "El fichero es válido y se subió con éxito.\n";
		} else {
			$mc .= "¡Posible ataque de subida de ficheros!\n";
		}

		// var_dump($_FILES);
		
		$mc=base64_encode($mc);
		$this->view("cargar",array("mc"=>$mc));
	}


	/* ******************************************
	**
	**   incorporacion del archivo csv a las db 
	**
	** ******************************************/
	public function cargar()
	{
		$this->view("cargar",array());
		
	}
	
	public function updatefilecsv(){
		$pagina=isset($_GET["pagina"])?$_GET["pagina"]:0 ;
		$registrosPorPagina=2000;
		
		$file = PATH . "/iduv.csv";
		// $totallineas=shell_exec(" wc -l \"$file\" |awk '{printf( \"%s\" , $1 )}' ");
		$totallineas=count(file($file));
		
		$file = PATH . "/iduv.csv";
		$handle = fopen($file,"r");
		$min=$pagina*$registrosPorPagina;
		$max=$registrosPorPagina+$pagina*$registrosPorPagina; // limite de lineas leidas.
		
		$mc="leyendo la pagina $pagina de ".round($totallineas / $registrosPorPagina,1)
			." <a href=\"?pagina=".($pagina+1)."\" >ir a la siguiente</a><br>\n";
		
		$mc.= $this->parcial($handle,$min,$max);
		
		// echo "mensaje=$mc<-<br>";
		$mc=base64_encode($mc);
		// $this->redirect("index","cargar?mc=$mc");
		$this->view("cargar",array("mc"=>$mc));
	
	}
	
	private function parcial($handle,$min,$max){
		
		$mc="";
		// ob_start();var_dump($_FILES);$mc== ob_get_contents() ; ob_end_clean();
		// if ( isset($_FILES['csv']))
		{ 
			// if ($_FILES['csv']['size'] > 0) 
			{
				
				//get the csv file
				$cliente=new clientes();
				$prestamo=new PMO_prestamo();
				$cuotas=new PMO_cuotas();
				$detalle=new PMO_detalleCuota();
				
				// variables: 
				{
					include("index_variables.php");
				}
				
				// $file = $_FILES['csv']['tmp_name'];
				// generalemnte en la ruta ~public_html/iduv
				// omitir registros previos:
	
				$c_registro=0;$c_add=0;
				//loop through the csv file and insert into database
				while ($data = fgetcsv($handle,1000,"|",'"')) {
					if ($c_registro==0){
						// primer registro es invalido ( encabezado )
						$keyVencimiento= array();$keyinventarioCSV=array();
						foreach ($vencimiento as $k=>$v) $keyVencimiento[] = $k ; 
						foreach ($invetarioCSV as $k=> $v ) $keyinventarioCSV[]= $k ;
						$ban=true; // todo en orden.
						
						foreach ($data as $k=>$v){
							if ( $data[0] == $keyVencimiento[0] ){
								// archivo vencimiento
								$variable="vencimiento";
								if ( $v != $keyVencimiento[$k] ){
									$ban=false ;
									$mc.="valores distintos $v no es ".$keyVencimiento[$k];
								}
								// $mc.="<div>Archivo vencimiento adquiriendo $v </div> \n";
							}
							if ( $data[0] == $keyinventarioCSV[0] ) {
								// archivo de inventario.
								$variable="invetarioCSV" ;
								if ( $v != $keyinventarioCSV[$k] ){
									$ban=false;
									$mc.="valores distintos $v no es ".$keyinventarioCSV[$k];
								}
								
							}
						} // comprobacion en orden.
						$mc .= "valores de : $variable ";
						$c_registro++; // cabecera del registro cvs . verificado que todas 
						// las columnas coincidan con el proyecto.
						
					}
					elseif ($c_registro <= $min ){
						// no hago nada.( registros salteados.
						
					}
					elseif ($c_registro >= $max ){
						// llege al final de los archivos previstos.
						$mc .= "fin de lectura de registros : <br>\m";
						$mc = "total de registros:$c_registro procesados<br>\n" . $mc;
						return $mc ;
						// break; // saliendo del bucle.
						
					}
					else 
					{
						// desenlace de registro:
						// $mc .= "<div>".$data[0]."->".$data[17]."</div><br>\n" ;
						if ($variable == "invetarioCSV" ){
							// identificando condiciones: 
							// condicion 1:
							foreach( ${$variable} as $k=>$v ){
								if (isset($v[2] )){
									if ($v[2] == 1 ){
										// busqueda obligatoria.
										$rgdatoC = ${$v[0]}->buscar($v[1] , $data[$v[3]] );
										if (isset($rgdatoC->nombre)){
											$mc .= "(". $v[3].")nombre:" . $rgdatoC->nombre ."<--id:" . $rgdatoC->id ."<--<br>\n" ;
											// EL CLIENTE:
											$rgCliente = $rgdatoC ; 
										}elseif (isset($rgdatoC->nroCuotas )){
											$mc .= "(". $v[3].")cantidad de cuotas:" . $rgdatoC->nroCuotas ."<--id:" . $rgdatoC->id ."<--<br>\n" ; 
											$rgPrestamo = $rgdatoC ;
										
										}else{
											$mc .= "no hay valor de asignacion tabla:".$v[0] ."campo:".$v[1]. "verificador:".$v[2] ." columna cvs:".$v[3] ."<br>\n" ;	
										}
									}
								}
							}
							if (isset($rgCliente)){
								if (!isset($rgPrestamo)){
									// agregar nuevo registro:
									include("index_addPrestamo.php");
									$mc .="agregado prestamo:$idPrestamo";
								}
							}
							elseif (isset($rgCuota)){
								if (!isset($rgPrestamo)){
									include("index_addPrestamo.php");
								}	
							}
							else{
								// no esta el cliente, agregar cliente y prestamo.
								include("index_addCliente.php");
								$mc.="agregando cliente:$idCliente";
								// agregar nuevo registro:
								include("index_addPrestamo.php");
								$mc .="agregado prestamo:$idPrestamo";
							}
						}
						elseif ($variable == "vencimiento" ){
							// cuota y otros.
							foreach( ${$variable} as $k=>$v ){
								if (isset($v[2] )){
									if ($v[2] == 1 ){
										// busqueda obligatoria.
										$rgdatoC = ${$v[0]}->buscar($v[1] , $data[$v[3]] );
										if (isset($rgdatoC->nombre)){
											$mc .= "(". $v[3].")nombre:" . $rgdatoC->nombre ."<--id:" . $rgdatoC->id ."<--<br>\n" ;
											// EL CLIENTE:
											$rgCliente = $rgdatoC ; 
										}elseif (isset($rgdatoC->nroCuotas )){
											$mc .= "(". $v[3].")cantidad de cuotas:" . $rgdatoC->nroCuotas ."<--id:" . $rgdatoC->id ."<--<br>\n" ; 
											$rgPrestamo = $rgdatoC ;
											
										}elseif (isset($rgdatoC->cuotaCapital  )){
											if (isset($rgPrestamo)){
												// hago la busqueda nuevamente con la condicion
												// que pertenezca a prestoamo actual.
												$rgdatoC->where("WHERE `idPrestamo` = \"".$rgPrestamo->id . "\" ");
												$rgdatoC = ${$v[0]}->buscar($v[1] , $data[$v[3]] );
												
												$mc .= "(". $v[3].")cuota de:" . $rgdatoC->cuotaCapital ."<--id:" . $rgdatoC->id ."<--<br>\n" ; 
												$rgCuota = $rgdatoC ;
											}
										}else{
											$mc .= "no hay valor de asignacion tabla:".$v[0] ." campo:".$v[1]. " verificador:".$v[2] ." columna cvs:".$v[3] ."<br>\n" ;	
										}
									}
								}
							}
														
							if (isset($rgCliente)){	$idCliente = $rgCliente->id ; }else{ $idCliente = "NEW"; }
							if (isset($rgPrestamo)){ $idPrestamo = $rgPrestamo->id ; }else{ $idPrestamo = "NEW"; }
							if (isset($rgCuota)){ $idcuota = $rgCuota->id ;}else{ $idcuota = "NEW"; }
							
							// cuando se agrega un registro si hay error tira falso.
							if ($idCliente == "NEW" ) {
								include("index_addClienteVTO.php");
								$mc.="agregando cliente : $idCliente <br>\n";
							}
							if ($idPrestamo == "NEW" and $idCliente ) {
								include("index_addPrestamoVTO.php");
								$mc.="agregando prestamo : $idPrestamo <br>\n";
							}
							if ($idcuota == "NEW" and $idPrestamo ) {
								include("index_addCuotaVTO.php");
								$mc.="agregando cuota : $idcuota <br>\n";
							}
							
						}
						
						
					}
					unset($rgPrestamo);
					unset($rgCliente);
					unset($rgCuota);
					
					$c_registro++;

				} ;
				//
				$mc = "total de registros:$c_registro procesados<br>\n" . $mc;
				//redirect
				// $mc.="-aqui-";

			}/*
			else
			{
				$mc="no se recibio el mensaje";
			}
			*/
		}

		return $mc ;
		
	}

	function palabras($min = 4, $max = 8)
	{		
		$vocales 	= array('a', 'e', 'i', 'o', 'u');
		$consonantes 	= array('b', 'c', 'd', 'f', 'g', 'j', 'l', 'm', 'n', 'p', 'r', 's', 't');
		$tamano 	= intval(rand($min, $max));
		$actual		= intval(rand(1,2));		
		$nombre 	= '';	
		for($x=0;$x<$tamano;$x++)
		{			
			if($actual == 0)
			{
				$actual	= 1;
				$pos 	= rand(0,count($vocales)-1);
				$nombre	.=  $vocales[$pos];				
			}
			else			
			{
				$actual	= 0;
				$pos 	= rand(0,count($consonantes)-1);
				$nombre	.=  $consonantes[$pos];				
			}				
		}
		return ucfirst($nombre);
	}
	
}
