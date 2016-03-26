<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("game_user")
		->label('ชื่อเข้าใช้')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['game_user']) ? $this->item['game_user']: '' )

	->field("game_pass")
		->label('รหัสผ่าน')
		// ->type('password')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['game_user']) ? $this->item['game_user']: '' );

$arr['body'] = $form->html();
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['m_id']);
$arr['title'] = 'ข้อมูลการเล่นเกมส์';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'member/confrim"></form>';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

$arr['width'] = 330;
echo json_encode($arr);