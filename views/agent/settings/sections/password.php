<?php

$form = new Form();
$form = $form->create()
	->url(URL."agent/change_password/{$this->me['agent_id']}?run=1")
	->addClass('js-submit-form')
	->method('post')
	// ->style('horizontal')

	->field("password_old")
		->type('password')
		->label('รหัสผ่านปัจจุบัน')
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")

	->field("password_new")
		->type('password')
		->label('รหัสผ่านใหม่')
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")

	->field("password_confirm")
		->type('password')
		->label('ยืนยันรหัสผ่าน')
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")

	->submit()
		->addClass("btn-submit btn btn-blue")
		->value("บันทึก");
?>


<div class="setting-title">รหัสผ่าน</div>

<div class="setting-description">เปลี่ยนรหัสผ่านของคุณ</div>

<section class="setting-section">
	<?=$form->html()?>
</section>
