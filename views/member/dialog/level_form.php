<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("lev_name")
		->label('ระดับ')
		->addClass('inputtext')
		->value( !empty($this->item['lev_name'])?$this->item['lev_name']:'' )
		->placeholder("")
		->maxlength(40)
		->required(true)
		->autocomplete("off")

	->field("lev_score")
		->label('คะแนน')
		->addClass('inputtext')
		->value( !empty($this->item['lev_score'])?$this->item['lev_score']:'' )
		->placeholder("")
		->maxlength(30)
		->required(true)
		->autocomplete("off")

	->field("lev_has_auto")
		->label('อัตโนมัติ')
		->text('<label class="checkbox"><input name="lev_has_auto" type="checkbox" value="1"'.( !empty($this->item['lev_has_auto'])? ' checked="1"':''  ).' /></label>');

if( !empty($this->item) ){
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['lev_id']);
}

$arr['body'] = $form->html();
$arr['title'] = 'เพิ่มระดับสมาชิก';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'member/save_level"></form>';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);