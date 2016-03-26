<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("name")
		->label('ชื่อ')
		->addClass('inputtext')
		->value( !empty($this->item['name'])?$this->item['name']:'' )
		->placeholder("")
		->maxlength(40)
		->required(true)
		->autocomplete("off")

	->field("email")
		->label('โทรศัพท์หรืออีเมล')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(30)
		->required(true)
		->autocomplete("off")
	->field("pass")
		->type('password')
		->label('รหัสผ่าน')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")

	->hr( '<div class="hr fsm fcg uiBoxGray noborder" style="position: relative;padding: 20px;margin: 0 -20px -20px;
"><span style="position: absolute;top: -8px;padding: 0 5px;font-weight: bold;left: 13px;"> ข้อมูลการเล่นเกม </span>')

	->field("m_user")
		->label('ชื่อผู้เข้าใช้')
		->addClass('inputtext')
		->value( !empty($this->item['user'])?$this->item['user']:'' )
		->maxlength(15)
		->required(true)
		->autocomplete("off")

	->field("m_pass")
		->type('password')
		->label('รหัสผ่าน')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")

	->hr( '</div>');

$arr['body'] = $form->html();
$arr['title'] = 'เพิ่มสมาชิกใหม่';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'member/add"></form>';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);