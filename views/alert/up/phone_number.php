<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("phone_number")
		->label('หมายเลยโทรศัพท์')
		->addClass('inputtext')
		->value( !empty($this->item['phone_number'])?$this->item['phone_number']:'' )
		->placeholder("")
		->maxlength(10)
		->required(true)
		->autocomplete("off");


$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->me['m_id']);
$arr['body'] = $form->html();

$arr['form'] = '<form class="js-submit-form" action="'.URL.'settings/change_phone_number"></form>';
$arr['title'] = "เพิ่มหมายเลยโทรศัพท์";
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ปิด</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';
$arr['width'] = 320;
echo json_encode($arr);