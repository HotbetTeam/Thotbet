<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	// ->attr('data-plugins',"formInsert")
	->addClass('form-insert');


	if( !empty($this->item) ){

		$form->field('user')->text( 

			'<div><strong>Member Account:</strong> ' .$this->post['username'].'</div>'.
			'<div><strong>วันที่:</strong> '.$this->fn->q('time')->normal( strtotime($this->post['pl_date']), true ).'</div>'
		);
	
	} else if( !empty($this->member) ){

		$form->field('user')->text( 

			'<div><strong>Name:</strong> ' .$this->member['name'].'</div>' .
			'<div><strong>Member Account:</strong> ' .$this->member['username'].'</div>'

		);

	 	$arr['hiddenInput'][] = array('name'=>'user', 'value'=>$this->member['username']);
	}
	else{
		$form 	->field('user')
			    ->type('text')
			    ->label('Member Account')
			    ->addClass('inputtext')	               
				->placeholder("รหัสสมาชิก")
				->maxlength(15)
				->required(true)
				->autocomplete("off");

	}	
		
	$form->hr(' <div class="fsm uiBoxYellow pam mbm">*กรณีที่ใส่จำนวนตัวเลขที่มีทศนิยมเกิน 2 ตำแหน่ง จะตัดให้เหลือแค่ 2 ตำแหน่ง และจำนวนที่เกินจะไม่มีการปัดขึ้น </div>')

		->field('pl_wagers')
		    ->type('text')
		    ->label('Wagers')
		    ->addClass('inputtext')	               
			->placeholder("จำนวนครั้งที่เล่น (ตัวเลขเท่านั้น)")
			->maxlength(7)
			->required(true)
			->autocomplete("off")
			->value( !empty($this->post['pl_wagers']) ? $this->post['pl_wagers']: '' )

		->field('pl_bet_amount')
		    ->type('text')
		    ->label('Bet amount')
		    ->addClass('inputtext')	               
			->placeholder("ยอดเดิมพัน (ตัวเลขเท่านั้น)")
			->maxlength(9)
			->required(true)
			->autocomplete("off")
			->value( !empty($this->post['pl_bet_amount']) ? round( $this->post['pl_bet_amount'],2, PHP_ROUND_HALF_DOWN ): '' )

		->field('pl_menber')
		    ->type('text')
		    ->label('Member')
		    ->addClass('inputtext')	               
			->placeholder("(ตัวเลขเท่านั้น)")
			->maxlength(9)
			->required(true)
			->autocomplete("off")
			->value( !empty($this->post['pl_menber']) ? round(  $this->post['pl_menber'],2, PHP_ROUND_HALF_DOWN): '' )

		->field('pl_actual')
		    ->type('text')
		    ->label('Actual betting Qty')
		    ->addClass('inputtext')	               
			->placeholder("ยอดเดิมพันทั้งสิ้น (ตัวเลขเท่านั้น)")
			->maxlength(9)
			->required(true)
			->autocomplete("off")
			->value( !empty($this->post['pl_actual']) ? round( $this->post['pl_actual'],2, PHP_ROUND_HALF_DOWN): '' );
 
		 
$arr['body'] = $form->html();
$arr['form'] = '<form class="form-insert-people js-submit-form" action="'.URL.'playing/update"></form>';

if( !empty($this->item) ){
    
    $arr['hiddenInput'][] = array('name'=>'id', 'value'=> $this->item['pl_id']);
    $arr['title'] = 'แก้ไขข้อมูลการเล่น';
}
else{
    
    $arr['title'] = '<div class="clearfix">		
		<div class="rfloat"><input data-plugins="datepicker" name="pl_date" data-options="'.
			( $this->fn->stringify(array('style'=>'normal'))).'"></div>
		<div>เพิ่มข้อมูลการเล่น</div>
	</div>';
}

$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">บันทึก</span></button>';

// $arr['width'] = 530;
echo json_encode($arr);