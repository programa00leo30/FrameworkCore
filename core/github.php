<?php
/*
 * clase para autoactualizacion de paquetes
 * desde github.
 * 
 */

class github{
	private $repositorio;
	private $branch;
	private $camino;
	private $usuario;
	
	public function __construct($usr,$branch,$repo,$camion="/"){
			$this->user=$usr;
			$this->branch=$granch;
			$this->camino=$camino;
			$this->repositorio=$repo;
	}
	public function verificar(){
		
		
	}
	public function update(){
		
		// $config = Yaml::parse(file_get_contents('config.yml'));
		$config = $this->camino;
		// payload desconosido.. pero es una reconstruccion de un array. posiblemenete las forma del repositorio.
		// $payload = json_decode($_POST['payload']);
		
		$LOCAL_REPO = $_SERVER['DOCUMENT_ROOT'] . "/" . //$payload->repository->name;
		$REMOTE_REPO = $payload->repository->url . ".git";

		if (file_exists($LOCAL_REPO)) {
			shell_exec("cd {$LOCAL_REPO} && git fetch --all && git reset --hard origin/master");
		} else {
			shell_exec("cd {$_SERVER['DOCUMENT_ROOT']} && git clone {$REMOTE_REPO} ");
		}
	}
	public function end(){
	}
}
