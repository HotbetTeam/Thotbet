<?php

$form = new Form();
$form = $form->create()
	->url(URL."partner/update/{$this->me['partner_id']}?run=1")
	->addClass('js-submit-form')
	->method('post')

	->field("partner_email")
		->type('email')
		->label('Email')
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")
		->value( $this->me['partner_email'] )

	->field("partner_name")
		->label('ชื่อ-สกุล')
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")
		->value( $this->me['partner_name'] )

	->field("partner_tel")
		->label('เบอร์โทรศัพท์')
		->addClass('inputtext')
		->maxlength(10)
		->autocomplete("off")
		->value( $this->me['partner_tel'] )

	->submit()
		->addClass("btn-submit btn btn-blue")
		->value("บันทึก");

?>

<div class="setting-title">โปรไฟล์</div>

<div class="setting-description">เปลี่ยนการตั้งค่าข้อมูลพื้นฐานของคุณ</div>

<section class="setting-section">
	<?=$form->html()?>
</section>
