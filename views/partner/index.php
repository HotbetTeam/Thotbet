<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container form-insert form-large js-submit-form')
    ->method('post')
    ->url(URL.'partner/register')

    ->field("partner_name")
        // ->label('ชื่อ*')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->placeholder("Full name")
        ->value( !empty($this->post['partner_name'])? $this->post['partner_name'] : '' )
        ->notify( !empty($this->error['partner_name']) ? $this->error['partner_name'] : '' )

    // set field
    ->field("partner_email")
        ->required(true)
        ->addClass('inputtext')
        ->autocomplete("off")
        ->placeholder("Phone or Email")
        ->value( !empty($this->post['partner_email'])? $this->post['partner_email'] : '' )
        ->notify( !empty($this->error['partner_email']) ? $this->error['partner_email'] : '' )

    ->field("partner_password")
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        ->placeholder("Password")
        ->notify( !empty($this->error['partner_password']) ? $this->error['partner_password'] : '' );


    if( !empty($this->captcha) ){

    $form->field("captcha")
        ->text('<div class="g-recaptcha" data-sitekey="'.RECAPTCHA_SITE_KEY.'"></div>')
        ->notify( !empty($this->error['captcha']) ? $this->error['captcha'] : '' );

    }

    $form->hr(  !empty($this->next)
        ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
        : '' 
    )    

    ->submit()
        ->addClass('btn btn-success btn-submit btn-large')
        
        ->value('Register');

?>

<section class="box1 clearfix">

	

	<div class="six-column lfloat" style="width: 395px;">

		<div class="header-text">
			<h1 class="large-title">Partner</h1>
		</div>
		

		<ul class="lists mvm">
			<li>สมัครสมาชิกวันนี้ รับฟรีโบนัสเพิ่มทันที10% (ตั้งแต่ 1,000 บาทขึ้นไป) ทุกๆยอดการเติมเงิน ฟรีโบนัสเพิ่มทันที10% (ตั้งแต่ 1,000 บาทขึ้นไป) ฟรีโบนัสสูงสุด จะจำกัดที่ยอด เติมเงิน</li>
			
			<li>100,000 บาท เท่าฟรีโบนัสสูงสุด 10,000 บาท</li>

			<li>รับฟรีโบนัสทุกครั้งที่สมัครสมาชิกและเติมเงินโดยไม่จำกัดจำนวนครั้งแต่อย่างใด</li>

			<li>ในกรณีเล่นเสีย ลูกค้าไม่ต้องจ่ายคืนฟรีโบนัสใดๆทั้งสิ้น เช่น เติมเงิน 1,000 บาทรับเครดิตทั้งสิ้น 1,100 บาท เล่นเสียทั้งหมดไม่ต้องจ่ายเงินเพิ่มแต่อย่างใด</li>

			<li>ในกรณีที่เล่นได้และมีและมีการถอนเงินกลับ ทางบริษัทจะหักฟรีโบนัส 10% กลับคืนเช่น เติมเงิน 1,000 บาท รับเครดิตทั้งสิ้น 1,100 บาท เล่นได้รวมเครดิตทั้งสิ้น 2,000 บาท เมื่อถอนกลับ จะได้รับยอดเงินทั้งสิ้น (2,000-100=1,900)</li>

			<li>ในกรณีที่เล่นไม่หมดแล้วมีการถอนเงินกลับฟรีโบนัสจะถูกหักกลับคืนทันที เช่น เติมเงิน 1,000 บาท รับเครดิตทั้งสิ้น 1,100 บาท เล่นเครดิตเหลือ 500 บาท แล้วถอนเงินกลับจะได้รับยอดเงินทั้งสิ้น (500-100=400 บาท)</li>
		</ul>

	</div>

	<div class="five-column rfloat" style="width: 370px;">

        <?php if( !empty($this->me['partner_id']) ){ ?>

        <div class="clearfix mbl">
            <a class="btn btn-danger btn-partner btn-large rfloat" href="<?=URL?>partner/manage">Partner Manage</a>
        </div>
        <?php }else{ ?>
		<div class="clearfix mbl">
			<a class="btn btn-danger btn-partner btn-large rfloat" href="<?=URL?>partner/login">Partner Login</a>
		</div>

        <div class="uiBoxWhite pal noborder" style="border-radius: 5px;">
		<h2 class="fcn">Partner Register</h2>
		<div class="form-container mts">
			<?= $form->html() ?>
		</div>
        <?php } ?>

        </div>
	</div>
</section>



