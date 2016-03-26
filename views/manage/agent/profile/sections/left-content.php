<?php

$f = new Form();
$form = $f->create()
	
	// set From
	// ->elem('div')
	->url( URL.'agent/live_update/'.$this->item['agent_id'] )
	->method('post')
	->attr('data-plugins', 'liveform')
	->addClass('form-insert')

	->field("agent_email")
    	->label('Email* ')
         ->type('email')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->item['agent_email'])? $this->item['agent_email'] : '' )

	->field("agent_name")
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
        ->value( !empty($this->item['agent_tel'])? $this->item['agent_tel'] : '' )

	->field("agent_note")
		->label('หมายเหตุ')
		->type( 'textarea' )
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		// ->maxlength(30)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->item['agent_note'])?$this->item['agent_note']:'' );

?>

<div class="profile-left-details form-insert-people" role="leftContent">

	<!-- <div class="pal">
		<ul class="profile-left-summary">
			<li><strong>แต้มแสดง:</strong> <?=$this->item['point_show']?></li>
			<li><strong>แต้มจริง:</strong> <?=$this->item['point']?></li>
			<li><strong>เป็นสมาชิกเมื่อ:</strong> <?= $this->fn->q('time')->normal( strtotime( $this->item['created'] ) ) ?></li>
			<li><strong>แก้ไขข้อมูลล่าสุด:</strong> <?= $this->fn->q('time')->stamp( $this->item['updated'] ) ?></li>
		</ul>
	</div> -->
	
	<div class="pal">
		<?=$form->html()?>
	</div>
	
</div>
