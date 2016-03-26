<?php

$levelSelector = "";
foreach ($this->level['lists'] as $key => $value) {
	$levelSelector .= '<option value="'.$value['lev_id'].'">'.$value['lev_name'].'</option>';
}

$levelSelector = '<select name="m_level_id" class="inputtext">'.$levelSelector.'</select>';

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("m_username")
		->label('ชื่อผู้เข้าใช้*')
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

	->field("m_level_id")
		->label('ระดับ')
		->text( $levelSelector )

	->field("m_note")
		->type('textarea')
		->label('หมายเหตุ')
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		->autocomplete("off");


$arr['body'] = $form->html();
$arr['title'] = 'เพิ่มสมาชิกใหม่';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'member/add"></form>';
$arr['button'] = '<a class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);