<?php

class Create_Form
{
	private $_url = null;
	private $_method = "get"; // type: get or post

	public function url($url="#"){
		$this->attr('action', $url);
		return $this;
	}

	function method($type="get"){
		$this->attr('method', $type);
		return $this;
	}

	private $_currentField = "__form";
	private $_field = null;
	private $_attr = null;
	private $_style = "vertical";
	private $_elem = "form";

	private function reset(){
		$this->_currentField = "__form";
		$this->_field = null;
		$this->_attr = null;
		$this->_style = "vertical";
	}

	public function _config(){
		$this->_currentField = "__form";
		$this->_field[$this->_currentField]['elem'] = "form";
		return $this;
	}

	public function style($style="vertical"){
		$this->_style = $style;
		return $this;
	}

	public function elem($elem){
		$this->_elem = $elem;
		return $this;
	}

	public function field($name){
		$this->_currentField = $name;
		$this->attr('id', $name);
		return $this;
	}

	public function hr($text=null){
		$this->_field[ '$hr_'.count($this->_field) ]['text'] = $text;
		return $this;
	}

	public function name($name){
		$this->attr('name', $name);
		return $this;
	}

	public function label($text){
		$this->_field[$this->_currentField]['label'] = $text;
		return $this;
	}

	public function notify($text){		
		$this->_field[$this->_currentField]['notify'] = $text;
		return $this;
	}

	public function value($value){
		$this->attr('value', $value);
		return $this;
	}

	public function placeholder($text){
		$this->attr('placeholder', $text);
		return $this;
	}

	public function autocomplete($name="off"){
		$this->attr('autocomplete', $name);
		return $this;
	}

	public function id($name="off"){
		$this->attr('id', $name);
		return $this;
	}

	public function type($type){
		$this->attr('type', $type);
		return $this;
	}

	public function required($req = true){
		$this->attr('required', $req);
		return $this;
	}

	public function attr($attr=null, $value=null){
		if(is_string($attr)){
			if( $value ){

	            $this->_field[$this->_currentField]['attr'][$attr] = $value;
                return $this;
                
            }else{

                if( isset($this->_field[$this->_currentField]['attr'][$attr]) )
                return $this->_field[$this->_currentField]['attr'][$attr];
                    
            }

		}elseif(is_array($attr)){
            $this->_field[$this->_currentField]['attr'] = $attr;
            return $this;
        }
	}

	public function addClass($class){
		$this->attr('class', $class);
		return $this;
	}

	public function button(){
		// $this->_field[ '$hr_'.count($this->_field) ]['text'] = $text;
		$this->_currentField = '$button_'.count($this->_field);
		return $this;
	}

	public function submit(){
		$this->_currentField = "__submit";
		return $this;
	}

	public function maxlength($length){
		$this->attr('maxlength', $length);
		return $this;
	}

	public function autofocus(){
		$this->attr('autofocus', "");
		return $this;
	}

	public function text($string){
		$this->_field[$this->_currentField]['__text'] = $string;
        return $this;
	}

	private function getAttr($attr){

		if( empty($attr) ) return "";
		$attribute_str="";
		foreach ($attr as $key => $value) {
			$attribute_str.=" {$key}=\"{$value}\"";
		}
		return $attribute_str;
	}

	public function html(){

		// print_r($this->_field);

		$field_str = ""; $actions = "";
		foreach ($this->_field as $key=>$value) {

			$keyx = explode("_", $key);
			if( $keyx[0] === '$hr' ){

				if(!empty($value['text'])){
					$field_str.=$value['text'];
				}
				else{
					$field_str.='<hr>';
				}
				
				continue;
			}

			if( $key == "__form" ){

				$value['attr']['class']  = isset($value['attr']['class'])
					? "{$value['attr']['class']} form-{$this->_style}"
					: "form-{$this->_style}";
					
				$form_attribute = $value['attr'];
				continue;
			}

			$value['attr']['type'] = (isset($value['attr']['type']))
				? $value['attr']['type']
				: "text";

			if( $key == "__submit" ){

				$attr = $value['attr'];
				$value = $attr['value']; unset($attr['value']);
				$actions.='<button type="submit"'.$this->getAttr( $attr ).'>'.$value.'</button>';
				continue;
			}
			elseif( $keyx[0] === '$button' ){

				$attr = $value['attr'];
				$value = $attr['value']; unset($attr['value']);
				
				$attr_str = $this->getAttr( $attr );

				$actions.= '<a'.$attr_str.'>'.$value.'</a>';
				// $value['attr']['type'] = "button";
				continue;
			}
			else{

				$value['attr']['name'] = isset($value['attr']['name'])
					? $value['attr']['name']
					: $value['attr']['id'];
			}

			$fieldId = isset($value['attr']['id'])
				? ' id="'.$value['attr']['id'].'_fieldset"'
				: "";

			$label = isset($value['label'])
				? '<label for="'.$value['attr']['id'].'" class="control-label">'.$value['label'].'</label>'
				: "";


			if( isset( $value['__text']) ){
				$string = $value['__text'];
			}
			elseif( $value['attr']['type'] === "textarea" ){


				$val = "";
				if( isset($value['attr']['value']) ){

					$val = $value['attr']['value'];
					unset($value['attr']['value']);
				}

				unset($value['attr']['type']);

				$string = '<textarea'.$this->getAttr( $value['attr'] ).'>'.$val.'</textarea>';
			}
			else{
				$string = '<input'.$this->getAttr( $value['attr'] ).'>';
			}


			$error = '';
			if( !empty($value['notify']) ){
				$error = ' has-error';
			}

			$field_str.='<fieldset'.$fieldId.' class="control-group'.$error.'">'.
				
				$label.

				'<div class="controls">'.
					$string.
					'<div class="notification">'.(!empty($value['notify'])? $value['notify']: "").'</div>'.
				'</div>'.

			'</fieldset>';
		}

		if( isset( $form_attribute ) && $this->_elem=="form"){
			$form_attribute['method'] = (in_array('post', $form_attribute))
				? "post"
				: "get";
		}

		$actions = !empty($actions)?'<div class="form-actions">'.$actions.'</div>':"";

		$this->reset();

		return '<'.$this->_elem.$this->getAttr( isset( $form_attribute )?$form_attribute:""  ).'>'.$field_str.$actions.'</'.$this->_elem.'>';
	}

}