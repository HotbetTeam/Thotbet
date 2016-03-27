<?php

$options = '';
foreach ($this->level['lists'] as $key => $item) {

	$cur = $this->item['level_id']==$item['lev_id'] ? ' selected="1"':'';;

	$options .= '<option'.$cur.' value="'.$item['lev_id'].'">'.$item['lev_name'].'</options>';
}

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	// ->style('horizontal')
	->addClass('form-insert')

	->field("name")
		->label('ระดับสมาชิก')
		->text(' <select name="level_id" class="inputtext">'.$options.'</select> ');

$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['m_id']);
$arr['body'] = $form->html();
$arr['title'] = 'เปลี่ยนระดับสมาชิก';	
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'member/change_level"></form>';
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';
$arr['width'] = 330;
echo json_encode($arr);