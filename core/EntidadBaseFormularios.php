<?php

class EntidadBaseFormularios{
	// esta clase controla la devolucion de html
	private $contadorid,$htm;
	// para la clase Entidad base y todos sus extenciones.
	public function __construct(){
		$this->contadorid = 0;
		//$this->htm = new html();
		
	}
	public function __call($name,$arguments){
		// por defecto llama a funcion de input basica:
		return $this->text( $arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4],$arguments[5] ).
			"-->>$name<<--";
		
	}
	
	public function hidden( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 // verificar si hay valor.. en caso de que no este no se envia el campo.
		$txt="";
		if ($valor !=""){
			
		 // return $txt;
		 return new html("input",array(
			"type" => "hidden"
			,"name"=>$campo
			,"id"=>$campo
			,"value"=>$valor
			),"",$extra);
		}else 
			return new html("div",array("style"=>"hidden"),$valor);
		
	}
	public function text( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){

		return new html("input",array(
			"type"=>"text"
			,"class"=>"form-control"
			,"name"=>$campo
			,"value"=>$valor
			,"placeholder"=>$placeholder
			),"");
	}
	
	public function password( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		// este tipo de campo no envia informacion.
		return new html("input",array(
			"type"=>"password"
			,"name"=>$campo
			,"id"=>$campo
			,"placeholder"=>$placeholder
			),"",$extra);
			
	}
	public function textarea( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 /*return "$tabulador<textarea class=\"form-control\" id='$campo' "
					."name=\"$campo\" $extra >$valor</textarea>\n";
		*/
		return new html("textarea",array(
			"name"=>$campo
			,"class"=>"form-control"
			,"id"=>$campo
		),$valor." ",$extra);
	}
	public function numerico( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 return new html("input",array(
			"type"=>"number"
			,"name"=>$campo
			,"id"=>$campo
			,"value"=>$valor
			),"",$extra);
		 /*
		 return "$tabulador<input type=\"number\" class=\"form-control\" "
					."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n";
		*/
	}
	public function moneda( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		return new html("input",array(
			"type"=>"number"
			,"name"=>$campo
			,"id"=>$campo
			,"data-number-to-fixed"=>"2"
			,"data-number-stepfactor"=>"100"
			,"class"=>"form-control currency"
			,"step"=>"0.01"
			,"value"=>$valor
			),"",$extra);
		/*	
		return "$tabulador<input type=\"number\"  "
						. "step=\"0.01\"   class=\"form-control currency\" "
						."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n";
		*/
	}
	public function fechahora( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 // para generar el campo de fecha / hora
		 // devo devolver este string para colocarlo al final de la pagina:
		 // $html->javascript("$(function() { $('#$campo').datetimepicker({ language: 'es', pick12HourFormat: false }); } );");
		 /*
		  * new html("div" ,array("class"=>"well"),
			new html("div",array("id"=>$campo,"class"=>"input-append date"),
				array(
		  * */
		 
		 return 
					new html("input",array(
							"type"=>"date" 
							,"data-format"=>"MM/dd/yyyy HH:mm:ss PP" 
							,"class"=>"form-control" 
							,"name"=>"$campo" 
							,"value"=>"$valor"
					),"",$extra)
					;
		/*
		 return <<<JAVAS
				<div class="well">
					<div id="$campo" class="input-append date">
						<input type="date" data-format="MM/dd/yyyy HH:mm:ss PP" class="form-control" name="$campo" $extra value="$valor" >
						<span class="add-on">
							<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
						</span>
					</div>
				</div>
JAVAS
;		*/
	}
	
	public function list( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=array() ){
		$label=isset($atr["label"])?$atr["label"]:$placeholder;
		$this->contadorid ++;
		// $campo = $this->contadorid . "_" . $campo;
		return new html("div",array("class"=>"input-group"),
			array(
				new html("input"
					,array(
						"type"=>"text"
						,"class"=>"form-control" 
						,"name"=>$campo
						, "id"=>$campo
						,"value"=>"$valor"
						,"placeholder"=>$placeholder
					)
					,""
					,$extra)
					,$this->botonlistar($lista,$campo,$label,$valor) 
				)
			);
						 
	}
	
	public function relacional( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=array() ){
		 $label=isset($atr["label"])?$atr["label"]:$placeholder;
		 $this->contadorid ++;
		 // $campo = $this->contadorid . "_" . $campo;
		 return new html("div",array("class"=>"input-group"),
			array(
				new html("input"
					,array(
						"type"=>"text" 
						,"class"=>"form-control" 
						,"name"=>$campo
						, "id"=>$campo
						,"value"=>"$valor"
						,"placeholder"=>$placeholder
					)
					,""
					,$extra)
				,$this->botonlistar($lista,$campo,$label,$valor)
			));
		/*	
		 return "$tabulador<input type=\"text\" class=\"form-control\" id=\"$campo\" "
						."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n".
						$this->botonlistar($lista,$campo,$label,$valor) ;
		*/
	}
	
    private function botonlistar($registro,$nombreID,$labelButon,$valor){
		/* se utiliza la variable $this->contadorid para identificar
		 * unequivocamente al registro relacional. */
		$tx = new html("div",array("class"=>"input-group-btn"));
		$tx->add(new html("botton", array(
				"type"=>"button"
				,"class"=>"btn btn-default dropdown-toggle" 
				,"data-toggle"=>"dropdown" 
				,"aria-haspopup"=>"true"
				,"aria-expanded"=>"false"
			),array( $labelButon, new html ("span",array("class"=>"caret")) ) ));

		$tx->add(new html("ul",array("class"=>"dropdown-menu")),
			array(
				new html("li",array(),new html("a",array("href"=>"javascript:void(0)"),"elige:"))
				, new html("li",array("role"=>"separator","class"=>"divider"))	
			)
		);
	
		
		foreach ($registro as $k=>$v){
			$tx->ul->add(new html("li",array()
				,new html("a",array(
					"href"=>"javascript:void(0)"
					,"selected"=> ($valor == $k)?"selected":""
					,"onclick"=>"$('#$nombreID').val('$k');"
					),$v)
				)
			);

		}
		return $tx;
    }
	
	
}
