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
		->label('Username On Web')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->item['username'])?$this->item['username']:'' )

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
			<li><strong>Username On Game:</strong> <?=$this->item['game_user']?></li>
			<li><strong>แต้มแสดง:</strong> <?=$this->item['point_show_str']?></li>
			<li><strong>แต้มจริง:</strong> <?=$this->item['point_str']?></li>
			<li><strong>เป็นสมาชิกเมื่อ:</strong> <?= $this->fn->q('time')->normal( strtotime( $this->item['created'] ) ) ?></li>
			<li><strong>แก้ไขข้อมูลล่าสุด:</strong> <?= $this->fn->q('time')->stamp( $this->item['updated'] ) ?></li>
			<li><strong>Agent:</strong> <?= !empty($this->item['agent_id']) ? '<a href="'.$this->item['agent']['url'].'">'.$this->item['agent']['agent_name'].'</a>' : '-'; ?></li>
		</ul>
	</div>
	
	<div class="pal">
		<?=$form->html()?>
	</div>
	
</div>
