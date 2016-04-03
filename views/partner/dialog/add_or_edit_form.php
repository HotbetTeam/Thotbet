<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('form-insert')
	->elem('div')
 
    ->field("partner_email")
    	->label('Email* ')
         ->type('email')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->item['partner_email'])? $this->item['partner_email'] : '' );

if( empty($this->item) ) {

	$form ->field("partner_password")
    	->label('รหัสผ่าน*')
        ->type('password')
        ->required(true)
        // ->placeholder("รหัสผ่าน")
        ->addClass('inputtext');
}

    $form ->field("partner_name")
        ->label('ชื่อ-สกุล*')
        ->addClass('inputtext')
        ->required(true)
          ->type('text')
        ->autocomplete("off")
        ->value( !empty($this->item['partner_name'])? $this->item['partner_name'] : '' )
    
     ->field("partner_tel")
        ->label('เบอร์โทร ')
        ->addClass('inputtext')
        ->required(true)
          ->type('tel')
        ->autocomplete("off")
        ->value( !empty($this->item['partner_tel'])? $this->item['partner_tel'] : '' );

$arr['body'] = $form->html();

if( empty($this->item) ) {

	$arr['title'] = 'Create Partner New';	
}
else{
	$arr['title'] = 'Edit Partner';	
    $arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['partner_id']);
	$arr['hiddenInput'][] = array('name'=>'next', 'value'=> URL.'manage/partner/' . $this->item['partner_id']);
}
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'partner/update"></form>';
$arr['button'] = '<a class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">บันทึก</span></button>';

echo json_encode($arr);