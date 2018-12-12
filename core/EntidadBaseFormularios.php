<?php

class EntidadBaseFormularios{
	// esta clase controla la devolucion de html
	// para la clase Entidad base y todos sus extenciones.
	public function __call($name,$arguments){
		// por defecto llama a funcion de input basica:
		return $this->text( $arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4],$arguments[5] ).
			"-->>$name<<--";
	}

	public function hidden( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 // verificar si hay valor.. en caso de que no este no se envia el campo.
		$txt="";
		if ($valor !=""){
			$txt.="$tabulador<input type=\"hidden\" "
			." name=\"$campo\" $extra value=\"$valor\" >\n";
			
		}
		 return $txt;
	}
	public function text( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 return "$tabulador<input type=\"text\" class=\"form-control\" "
					."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\" >\n";
	}
	public function password( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		// este tipo de campo no envia informacion.
		 return "$tabulador<input type=\"password\" class=\"form-control\" "
					."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"\" >\n";
	}
	public function textarea( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 return "$tabulador<textarea class=\"form-control\" id='$campo' "
					."name=\"$campo\" $extra >$valor</textarea>\n";
	}
	public function numerico( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 return "$tabulador<input type=\"number\" class=\"form-control\" "
					."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n";
	}
	public function moneda( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 return "$tabulador<input type=\"number\" class=\"form-control\" "
						. "step=\"0.01\" data-number-to-fixed=\"2\" data-number-stepfactor=\"100\" class=\"form-control currency\" "
						."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n";
	}
	public function fechahora( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=null ){
		 // para generar el campo de fecha / hora
		 // devo devolver este string para colocarlo al final de la pagina:
		 // $html->javascript("$(function() { $('#$campo').datetimepicker({ language: 'es', pick12HourFormat: false }); } );");
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
;
	}
	public function list( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=array() ){
		 $label=isset($atr["label"])?$atr["label"]:$placeholder;
		 return "$tabulador<input type=\"text\" class=\"form-control\" id=\"$campo\" "
						."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n".
						$this->botonlistar($lista,$campo,$label,$valor) ;
	}
	public function relacional( $campo,$valor,$tabulador="\t\t\t",$placeholder="placeholder",$extra="" ,$lista=array() ){
		 $label=isset($atr["label"])?$atr["label"]:$placeholder;
		 return "$tabulador<input type=\"text\" class=\"form-control\" id=\"$campo\" "
						."placeholder=\"$placeholder\" name=\"$campo\" $extra value=\"$valor\">\n".
						$this->botonlistar($lista,$campo,$label,$valor) ;
	}
	
    private function botonlistar($registro,$nombreID,$labelButon,$valor){
		$tx=<<<texto
				<div class="input-group-btn">
					
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						$labelButon<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="javascript:void(0)" >elige:</a></li>
						<li role="separator" class="divider"></li>
texto
;	
		foreach ($registro as $k=>$v){
			if ($valor == $k) $sel="selected=selected" ; else $sel="" ;
			
			$js=" onclick=\"$('#$nombreID').val('$k');\" ";
			
			$tx = <<<textofor
$tx
						<li><a href="javascript:void(0)" $js $sel >$v</a></li>
textofor
;
		}
			$tx = <<<textoultm
$tx
					</ul>
				</div><!-- /btn-group -->
textoultm
;
		return $tx;
    }
	
	
}
