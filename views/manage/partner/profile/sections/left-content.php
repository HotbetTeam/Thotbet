<?php

$f = new Form();
$form = $f->create()
	
	// set From
	// ->elem('div')
	->url( URL.'partner/live_update/'.$this->item['partner_id'] )
	->method('post')
	->attr('data-plugins', 'liveform')
	->addClass('form-insert')

	->field("partner_email")
    	->label('Email* ')
         ->type('email')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->item['partner_email'])? $this->item['partner_email'] : '' )

	->field("partner_name")
        ->label('ชื่อ*')
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
        ->value( !empty($this->item['partner_tel'])? $this->item['partner_tel'] : '' )

	->field("partner_note")
		->label('หมายเหตุ')
		->type( 'textarea' )
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		// ->maxlength(30)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->item['partner_note'])?$this->item['partner_note']:'' );

?>

<div class="profile-left-details form-insert-people" role="leftContent">

	<div class="phl mvl">
		<ul class="profile-left-summary">
			<li><strong>เป็นสมาชิกเมื่อ:</strong> <?= $this->fn->q('time')->normal( strtotime( $this->item['partner_created'] ) ) ?></li>
			<li><strong>แก้ไขข้อมูลล่าสุด:</strong> <?= $this->fn->q('time')->stamp( $this->item['partner_updated'] ) ?></li>
		</ul>
	</div>
	
	<div class="phl mvl">
		<?=$form->html()?>
	</div>
	
</div>
