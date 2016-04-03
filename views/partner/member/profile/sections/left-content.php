<?php

$f = new Form();
$form = $f->create()
	
	// set From
	// ->elem('div')
	->url( URL.'partner/member/live_update/'.$this->item['m_id'] )
	->method('post')
	->attr('data-plugins', 'liveform')
	->addClass('form-insert')

	->field("username")
		->label('Username')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['username'])?$this->item['username']:'' )

	->field("name")
		->label('ชื่อ')
		->addClass('inputtext')
		->value( !empty($this->item['name'])?$this->item['name']:'' )
		->placeholder("")
		->maxlength(40)
		->required(true)
		->autocomplete("off")

	->field("email")
		->label('Email')
		->addClass('inputtext')
		->maxlength(30)
		->autocomplete("off")
		->value( !empty($this->item['email'])?$this->item['email']:'' )

	->field("phone_number")
		->label('โทรศัพท์')
		->addClass('inputtext')
		->maxlength(10)
		->autocomplete("off")
		->value( !empty($this->item['phone_number'])?$this->item['phone_number']:'' )

	->field("note")
		->label('หมายเหตุ')
		->type( 'textarea' )
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		->autocomplete("off")
		->value( !empty($this->item['note'])?$this->item['note']:'' );

?>

<div class="profile-left-details form-insert-people" role="leftContent">

	<div class="pal">
		<ul class="profile-left-summary">
			<li><strong>ระดับ:</strong> <?=$this->item['level_name']?></li>
			<li><strong>แต้ม:</strong> <?=$this->item['point_show']?></li>
			<li><strong>เป็นสมาชิกเมื่อ:</strong> <?= $this->fn->q('time')->normal( strtotime( $this->item['created'] ) ) ?></li>
			<li><strong>แก้ไขข้อมูลล่าสุด:</strong> <?= $this->fn->q('time')->stamp( $this->item['updated'] ) ?></li>
		</ul>
	</div>
	
	<div class="pal">
		<?=$form->html()?>
	</div>
	
</div>
