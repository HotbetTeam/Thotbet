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
		->value( !empty($this->item['username'])?$this->item['username']:'' )

	->field("m_name")
		->label('ชื่อ*')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(20)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['name'])?$this->item['name']:'' )

	->field("m_email")
		->label('อีเมล์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(30)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->item['email'])?$this->item['email']:'' )

	->field("m_phone_number")
		->label('เบอร์โทรศัพท์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(10)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->item['phone_number'])?$this->item['phone_number']:'' )

	->field("m_note")
		->type('textarea')
		->label('หมายเหตุ')
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		->autocomplete("off")
		->value( !empty($this->item['note'])?$this->item['note']:'' );


$arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['m_id']);
$arr['body'] = $form->html();
$arr['title'] = 'แก้ไขสมาชิก';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'agent/member/edit"></form>';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);