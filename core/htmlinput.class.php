<?php

/*
	funciones para crear formularios:
	
*/

class htmlinput {


	private $imageKeys = array();
	
	private function getElementHtml($tag, $attributes, $content = false) {
		$code = '<' . $tag;
		foreach ($attributes as $attribute => $value) {
			$code .= ' ' . $attribute . '="' . htmlentities(stripslashes($value), ENT_COMPAT) . '"';
		}

		if ($content === false || $content === null) {
			$code .= ' />';
		} else {
			if ($content != -1 ){
				$code .= '>' . $content . '</' . $tag . '>';
			}
			else{
				// no tiene cierre.
				$code .= '>' ;
			}
		}

		return $code;
	}
	
	
	public function registerImageKey($key, $value) {
		$this->imageKeys[$key] = $value;
	}

	public function getImageKeys() {
		return $this->imageKeys;
	}
	public function getFormTextHtml($name,$accion,$atributos=array() ){
	
		$defaultAttributes = array(
			'id' => $name,
			'name' => $name,
			'method' => 'post' ,
			'action' => $accion 
		);

		$finalAttributes = array_merge($defaultAttributes, $atributos);

		return $this->getElementHtml('form', $finalAttributes, -1 );
		
		// return "<form name=\"$nombre\" method=\"POST\" action=\"".$this->url($controlador,$accion)."\" $otros >";
	}
	public function getInputTextHtml($name, $currentValue, $attributes = array()) {
		$defaultAttributes = array(
			'id' => $name,
			'name' => $name
		);

		$finalAttributes = array_merge($defaultAttributes, $attributes);
		if ($currentValue !== null) {
			$finalAttributes['value'] = $currentValue;
		}

		return $this->getElementHtml('input', $finalAttributes, false);
	}

	public function getOptionGroup($options, $currentValue) {
		$content = '';
		foreach ($options as $optionKey => $optionValue) {
			if (is_array($optionValue)) {
				$content .= '<optgroup label="' . $optionKey . '">' 
					. $this->getOptionGroup($optionValue, $currentValue) . '</optgroup>';
			} 
			else {
				$optionAttributes = array();
				if ($currentValue == $optionKey) {
					$optionAttributes['selected'] = 'selected';
				}
				$content .= $this->getOptionHtml($optionKey, $optionValue, $optionAttributes);
			}
		}

		return $content;
	}

	public function getOptionHtml($value, $content, $attributes = array()) {
		$defaultAttributes = array(
			'value' => $value
		);

		$finalAttributes = array_merge($defaultAttributes, $attributes);

		return $this->getElementHtml('option', $finalAttributes, $content);
	}

	public function getSelectHtml($name, $currentValue, $options, $attributes = array()) {
		$defaultAttributes = array(
			'size' => 1,
			'id' => $name,
			'name' => $name
		);

		$finalAttributes = array_merge($defaultAttributes, $attributes);
		$content = $this->getOptionGroup($options, $currentValue);

		return $this->getElementHtml('select', $finalAttributes, $content);
	}

	public function getCheckboxHtml($name, $currentValue, $attributes = array()) {
		$defaultAttributes = array(
			'type' => 'checkbox',
			'id' => $name,
			'name' => $name,
			'value' => isset($attributes['value']) ? $attributes['value'] : 'On'
		);

		$finalAttributes = array_merge($defaultAttributes, $attributes);
		if ($currentValue == $finalAttributes['value']) {
			$finalAttributes['checked'] = 'checked';
		}

		return $this->getElementHtml('input', $finalAttributes, false);
	}

	public function getButton($value, $output = null) {
		$escaped = false;
		$finalValue = $value[0] === '&' ? $value : htmlentities($value);
		if ($output === null) {
			$output = $value;
		} else {
			$escaped = true;
		}

		$code = '<input type="button" value="' . $finalValue 
			. '" data-output="' . $output . '"' . ($escaped ? ' data-escaped="true"' : '') . ' />';
		return $code;
	}


}
