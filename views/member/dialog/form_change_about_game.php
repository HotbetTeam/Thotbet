<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

	->field("game_user")
		->label('Member Account')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['game_user']) ? $this->item['game_user']: '' )

	->field("game_pass")
		->type('password')
		->label('Password')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off");

$arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['m_id']);
$arr['body'] = $form->html();
$arr['title'] = 'ข้อมูลการเล่นเกมส์';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'member/change_about_game"></form>';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ปิด</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';

$arr['width'] = 330;
echo json_encode($arr);