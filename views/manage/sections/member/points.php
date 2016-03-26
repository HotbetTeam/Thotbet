<?php

$f = new Form();
$form = $f->create()
	
	// set From
	// ->elem('div')
	->method('post')
	->url( URL .'member/change_point' )
	// ->attr('data-plugins',"formInsert")
	->addClass('form-insert js-submit-form')
// 

	->field("menber")
		->label('Menber Point (%)')
		->addClass('inputtext')
		->value( !empty($this->post['menber'])? $this->post['menber']*100:'' )
		->placeholder("")
		->maxlength(6)
		// ->required(true)
		->autocomplete("off")

	->field("actual")
		->label('Actual betting Qty Point (%)')
		->addClass('inputtext')
		->value( !empty($this->post['actual'])? $this->post['actual']*100:'' )
		->placeholder("")
		->maxlength(6)
		// ->required(true)
		->autocomplete("off")

	->submit()
		->addClass('btn btn-primary')
		->value('บันทึก');

?>

<div class="settings-content-title">คำนวณแต้มสมาชิก</div>
<div class="settings-content-description">ปรับเปลี่ยนค่าการคำนวณแต้มสมาชิก</div>

<hr class="settings-content-hr">
<section class="settings-content-description">
	การเปลี่ยนแปลงค่าการคำนวณแต้มสมาชิกค่าใหม่ จะเริ่มคำนวณหลังจากการบันทึก และค่าใหม่นี้จะไม่ส่งผลถึงค่าคำนวณ<span class="fcr">แต้มเดิม</span>ของสมาชิก
</section>

<section class="settings-content-section"><?=$form->html()?></section>