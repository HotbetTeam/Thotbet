<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("name")
		->label('ชื่อ')
		->addClass('inputtext')
		
		->placeholder("")
		->maxlength(40)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['name'])?$this->item['name']:'' )

	->field("email")
		->label('อีเมล์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(30)
		->autocomplete("off")
		->value( !empty($this->item['email'])?$this->item['email']:'' )

	->field("phone_number")
		->label('โทรศัพท์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(10)
		->autocomplete("off")
		->value( !empty($this->item['phone_number'])?$this->item['phone_number']:'' );


$arr['body'] = $form->html();
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'users/edit"></form>';
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['user_id']);

switch ($this->item['access_id']) {
	case 1:
		$arr['title'] = 'แก้ไขผู้ดูแลระบบ';
		break;

	case 3:
		$arr['title'] = 'แก้ไขข้อมูล Operator';
		break;
	
	default:
		$arr['title'] = 'แก้ไขบัญชีผู้เข้าใช้';
		break;
}

$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);