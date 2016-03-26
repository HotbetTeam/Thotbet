<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	// ->attr('data-plugins',"formInsert")
	->addClass('form-insert')
// 

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
		->value( !empty($this->item['email'])?$this->item['email']:'' )
		->placeholder("")
		->maxlength(15)
		->required(true)
		->autocomplete("off")

	->field("user")
		->label('ชื่อผู้เข้าใช้')
		->addClass('inputtext')
		->value( !empty($this->item['user'])?$this->item['user']:'' )
		->placeholder("")
		->maxlength(15)
		->required(true)
		->autocomplete("off")
			// ->option('inputtext')

	->field("pass")
		->type('password')
		->label('รหัสผ่าน')
		->addClass('inputtext')
		->value( !empty($this->item['name'])?$this->item['name']:'' )
		->placeholder("")
		->maxlength(30)
		->required(true)
		->autocomplete("off");

$arr['body'] = $form->html();

$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'users/set"></form>';
$arr['title'] = 'เพิ่มสมาชิกใหม่';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

$arr['width'] = 330;
echo json_encode($arr);