<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container form-insert form-large')
    ->method('post')
    ->url(URL.'partner/login')

    // set field
    ->field("email")
        ->placeholder("username or email")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['email'])? $this->post['email'] : '' )
        ->notify( !empty($this->error['email']) ? $this->error['email'] : '' )

    ->field("pass")
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        ->placeholder("password")
        ->notify( !empty($this->error['pass']) ? $this->error['pass'] : '' );


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
        ->addClass('btn btn-blue btn-submit btn-large')
        
        ->value('เข้าสู่ระบบ')

    ->button()
        ->addClass('btn btn-link btn-large fsm or')
        ->attr('href', URL.'partner/register')
        ->value('Or Register');

?>

<div class="bgs o">
    <div class="bg" style="background-image: url(<?=IMAGES?>carousel/c10.jpg);display: block;"></div>
</div>

<div class="section">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <div class="text">
                <h2 >partner <span><?=PAGE_TITLE?></span><a href="<?=URL?>partner" style="color:#fff"><i class="icon-home mrs"></i>หน้าแรก</a></h2>
            </div>
            <div class="subtext"><?=PAGE_ADDRESS?></div>
        </div>

        <div class="login-container-wrapper">

            <div class="login-container">
                
                <div class="login-title">ลงชื่อเข้าใช้ partner</div>

                <?=$form->html()?>
            
            </div>

        </div>
    </div>
<!-- /content-wrapper -->

</div>
<!-- /section -->

