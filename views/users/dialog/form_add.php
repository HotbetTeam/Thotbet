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
		->placeholder("")
		->maxlength(30)
		->required(true)
		->autocomplete("off");

$arr['hiddenInput'][] = array('name'=>'access_id','value'=>$this->access_id);
$arr['body'] = $form->html();
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'users/add"></form>';


switch ($this->access_id) {
	case 1:
		$arr['title'] = 'เพิ่มผู้ดูแลระบบ';
		break;

	case 3:
		$arr['title'] = 'เพิ่ม Operator';
		break;
	
	default:
		$arr['title'] = 'เพิ่มบัญชีผู้เข้าใช้';
		break;
}

$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);