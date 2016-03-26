<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container form-insert form-large js-submit-form')
    ->method('post')
    ->url(URL.'agent/register')

    ->field("agent_name")
        // ->label('ชื่อ*')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->placeholder("Full name")
        ->value( !empty($this->post['agent_name'])? $this->post['agent_name'] : '' )
        ->notify( !empty($this->error['agent_name']) ? $this->error['agent_name'] : '' )

    // set field
    ->field("agent_email")
        ->type('email')
        ->required(true)
        ->addClass('inputtext')
        ->autocomplete("off")
        ->placeholder("Phone or agent_email")
        ->value( !empty($this->post['agent_email'])? $this->post['agent_email'] : '' )
        ->notify( !empty($this->error['agent_email']) ? $this->error['agent_email'] : '' )

    ->field("agent_password")
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        ->placeholder("Password")
        ->notify( !empty($this->error['agent_password']) ? $this->error['agent_password'] : '' );


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
			<h1 class="large-title">Agent</h1>
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

        <?php if( !empty($this->me['agent_id']) ){ ?>

        <div class="clearfix mbl">
            <a class="btn btn-danger btn-agent btn-large rfloat" href="<?=URL?>agent/manage" style="">Agent Manage</a>
        </div>
        <?php }else{ ?>
		<div class="clearfix mbl">
			<a class="btn btn-danger btn-agent btn-large rfloat" href="<?=URL?>agent/login" style="">Agent Login</a>
		</div>

		<h2>Agent Register</h2>
		<div class="form-container mrl">
			<?= $form->html() ?>
		</div>
        <?php } ?>
	</div>
</section>



