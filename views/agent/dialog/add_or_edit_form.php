<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('form-insert')
	->elem('div')
 
    ->field("agent_email")
    	->label('Email* ')
         ->type('email')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->item['agent_email'])? $this->item['agent_email'] : '' );

if( empty($this->item) ) {

	$form ->field("agent_password")
    	->label('รหัสผ่าน*')
        ->type('password')
        ->required(true)
        // ->placeholder("รหัสผ่าน")
        ->addClass('inputtext');
}

    $form ->field("agent_name")
        ->label('ชื่อ*')
        ->addClass('inputtext')
        ->required(true)
          ->type('text')
        ->autocomplete("off")
        ->value( !empty($this->item['agent_name'])? $this->item['agent_name'] : '' )
    
     ->field("agent_tel")
        ->label('เบอร์โทร ')
        ->addClass('inputtext')
        ->required(true)
          ->type('tel')
        ->autocomplete("off")
        ->value( !empty($this->item['agent_tel'])? $this->item['agent_tel'] : '' );

$arr['body'] = $form->html();

if( empty($this->item) ) {

	$arr['title'] = 'Create Agent New';	
}
else{
	$arr['title'] = 'Edit Agent';	
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['agent_id']);
}
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'agent/update"></form>';
$arr['button'] = '<a class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);