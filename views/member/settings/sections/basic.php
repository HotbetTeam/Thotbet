<?php
$f = new Form();
$form = $f->create()
	
	// set From
	->url( URL.'settings/basic' )
	->method('post')
	->addClass('form-insert')

/*	->field("username")
		->label('ชื่อผู้เข้าใช้*')
		->addClass('inputtext')
		->maxlength(15)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->me['username'])?$this->me['username']:'' )
		->notify( !empty($this->me['error']['username'])? $this->me['error']['username']:'' )
*/
	->field("name")
		->label('ชื่อ*')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(20)
		->required(true)
		->autocomplete("off")
		->value( !empty($this->me['name'])?$this->me['name']:'' )
		->notify( !empty($this->me['error']['name'])? $this->me['error']['name']:'' )

	->field("email")
		->label('อีเมล์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(30)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->me['email'])?$this->me['email']:'' )
		->notify( !empty($this->me['error']['email'])? $this->me['error']['email']:'' )

	->field("phone_number")
		->label('เบอร์โทรศัพท์')
		->addClass('inputtext')
		->placeholder("")
		->maxlength(10)
		// ->required(true)
		->autocomplete("off")
		->value( !empty($this->me['phone_number'])?$this->me['phone_number']:'' )
		->notify( !empty($this->me['error']['phone_number'])? $this->me['error']['phone_number']:'' )

	->submit()
		->addClass('btn btn-submit btn-yellow')
		->value('บันทึก');

?>
<section class="settings-section">
	<h3>ตั้งค่าทั้วไป</h3>

	<?php if( !empty($this->me['error']) ){ ?>
	<div class="settings-form-error">ไม่สามารถบันทึกข้อมูลได้</div>
	<?php } ?>

	<?php if( !empty($this->message) ){ ?>
	<div class="settings-form-alert"><?=$this->message?></div>
	<?php } ?>
	<?=$form->html();?>
</section>