<?php

/* *******************************************
 * clase que controla la parametrizacion 
 * de entrada al sistema.
 * 
 * *********************************************/
 
class Parametros {
	
	private $parametros;
	private $valores;
	
	
	public function __construct(){
		if (isset($_GET)){
			foreach ($_GET as $k=>$v){
				$this->parametros["GET"][$k]=$v;
				$this->valores[$k]=$v;
			}
		}
		if (isset($_POST)){
			foreach ($_POST as $k=>$v){
				$this->parametros["POST"][$k]=$v;
				$this->valores[$k]=$v;
			}
		}
		if (isset($_COOKIE)){
			foreach ($_COOKIE as $k=>$v){
				$this->parametros["COOKIE"][$k]=$v;
				$this->valores[$k]=$v;
			}
		}
		if (isset($_SERVER["argv"])){
			foreach ($_SERVER["argv"] as $k=>$v){
				$this->valores[$k]=$v;
				$this->parametros["ARG"][$k]=$v;
			}
		}
	}
	
	public function __get($campo){
		if (isset($this->valor[$campo])){
			return $this->valor[$campo];
		}
		else return null;
		
	}
	public function _GET($campo){
		if (isset($this->parametros["GET"][$campo])){
			return $this->parametros["GET"][$campo];
		}else return null;
	}
	public function _POST($campo){
		if (isset($this->parametros["POST"][$campo])){
			return $this->parametros["POST"][$campo];
		}else return null;
	}
	public function _COOKIE($campo){
		if (isset($this->parametros["COOKIE"][$campo])){
			return $this->parametros["COOKIE"][$campo];
		}else return null;
	}
	public function getAllPOST(){
		return $this->parametros["POST"];
	}
}
