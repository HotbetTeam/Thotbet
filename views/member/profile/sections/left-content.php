<?php

$levelSelector = "";
foreach ($this->level['lists'] as $key => $value) {

	$sed = $value['lev_id']==$this->item['level_id'] ? ' selected="1"':'';
	$levelSelector .= '<option'.$sed.' value="'.$value['lev_id'].'">'.$value['lev_name'].'</option>';
}

$levelSelector = '<select name="level_id" class="inputtext">'.$levelSelector.'</select>';

$f = new Form();
$form = $f->create()
	
	// set From
	// ->elem('div')
	->url( URL.'member/live_update/'.$this->item['m_id'] )
	->method('post')
	->attr('data-plugins', 'liveform')
	->addClass('form-insert')

	->field("name")
		->label('ชื่อ')
		->addClass('inputtext')
		->value( !empty($this->item['name'])?$this->item['name']:'' )
		->placeholder("")
		->maxlength(40)
		->required(true)
		->autocomplete("off")

	->field("email")
		->label('อีเมล')
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

	->field("level_id")
		->label('ระดับ')
		->text( $levelSelector )

	->field("username")
		->label('ชื่อผู้เข้าใช้')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['username'])?$this->item['username']:'' )

/*	->field("password")
		->type( 'password' )
		->label('รหัสผ่าน')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")*/

	->field("note")
		->label('หมายเหตุ')
		->type( 'textarea' )
		->addClass('inputtext')
		->attr('data-plugins', 'autosize')
		// ->maxlength(30)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->item['note'])?$this->item['note']:'' );

?>

<div class="profile-left-details form-insert-people" role="leftContent">

	<div class="pal">
		<ul class="profile-left-summary">
			<li><strong>แต้มแสดง:</strong> <?=$this->item['point_show']?></li>
			<li><strong>แต้มจริง:</strong> <?=$this->item['point']?></li>
			<li><strong>เป็นสมาชิกเมื่อ:</strong> <?= $this->fn->q('time')->normal( strtotime( $this->item['created'] ) ) ?></li>
			<li><strong>แก้ไขข้อมูลล่าสุด:</strong> <?= $this->fn->q('time')->stamp( $this->item['updated'] ) ?></li>
		</ul>
	</div>
	
	<div class="pal">
		<?=$form->html()?>
	</div>
	
</div>
