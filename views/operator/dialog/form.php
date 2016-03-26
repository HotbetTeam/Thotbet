<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	// ->attr('data-plugins',"formInsert")
	->addClass('form-insert')
        ->field("op_customer")
		->label('ชื่อลูกค้า')
		->addClass('inputtext')
		->value( !empty($this->item['name'])?$this->item['op_customer']:'' )
		->placeholder("กรอกชื่อ")
		->maxlength(40)
		->required(true)
		->autocomplete("off")
	->field("op_text") 
                ->type('textarea')
                ->label('ข้อมูลการสนทนา')
		->addClass('inputtext TextW')
				->attr('style', "min-height: 200px;")
                ->attr('data-plugins', 'editor')
                ->attr('data-options',$this->fn->stringify(array('height'=>200)))
		->value( !empty($this->item['op_text'])?$this->item['op_text']:'' )
		->placeholder("บันทึกการสนทนา")		
		->required(true)                
		->autocomplete("off");
		 

$arr['body'] = $form->html();

$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'operator/set"></form>';

if( !empty($this->item) ){
    
    $arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['op_id']);
    $arr['title'] = 'แก้ไขสมาชิกใหม่';
}
else{
    
    $arr['title'] = 'เพิ่มสมาชิกใหม่';
}

$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

$arr['width'] = 780;
echo json_encode($arr);