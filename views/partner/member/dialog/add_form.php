<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("m_username")
		->label('Username*')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['m_username'])?$this->item['m_username']:'' )

	->field("m_password")
		->type('password')
		->label('รหัสผ่าน*')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")

	->field("m_name")
		->label('ชื่อ*')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(20)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['m_name'])?$this->item['m_name']:'' )

	->field("m_email")
		->label('อีเมล์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(30)
		// ->required(true)
		->autocomplete("off")

	->field("m_phone_number")
		->label('เบอร์โทรศัพท์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(10)
		// ->required(true)
		->autocomplete("off")

	->field("m_note")
		->type('textarea')
		->label('หมายเหตุ')
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		->autocomplete("off");


$arr['body'] = $form->html();
$arr['title'] = 'เพิ่มสมาชิกใหม่';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'partner/member/add"></form>';
$arr['button'] = '<a class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);