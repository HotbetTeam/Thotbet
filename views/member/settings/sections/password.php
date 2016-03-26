<?php

$f = new Form();
$form = $f->create()
	
	// set From
	// ->elem('div')
	->url( URL.'settings/password' )
	->method('post')
	->addClass('form-insert')

	->field("password_new")
		->label('รหัสผ่าน')
		->type('password')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")

	->field("password_confirm")
		->label('พิมพ์อีกครั้ง')
		->type('password')
		->addClass('inputtext')
		->maxlength(30)
		->required(true)
		->autocomplete("off")

	->submit()
		->addClass('btn btn-submit btn-yellow')
		->value('บันทึก');

?>

<section class="settings-section">
	<h3>เปลี่ยนรหัสผ่าน</h3>

	<?php if( !empty($this->post['password']['error']) ){ ?>
		<div class="settings-form-error">

		<strong>ไม่สามารถบันทึกข้อมูลได้:</strong>
		
		<ul>
			<?php foreach ($this->post['password']['error'] as $key => $value) { ?>
				<li><?=$value?></li>
			<?php } ?>
			
		</ul>
		</div>
	<?php } ?>

	<?php if( !empty($this->post['password']['message']) ){ ?>
		<div class="settings-form-alert"><?=$this->post['password']['message']?></div>
	<?php } ?>

	<?=$form->html();?>
</section>