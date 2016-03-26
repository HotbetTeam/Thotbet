<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container standard form-insert form-large')
    ->method('post')
    ->url(URL.'login')

    // set field
    ->field("email")
        ->placeholder("ชื่อผู้ใช้")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['email'])? $this->post['email'] : '' )
        ->notify( !empty($this->error['email']) ? $this->error['email'] : '' )

    ->field("pass")
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        ->placeholder("รหัสผ่าน")
        ->value( !empty($this->post['pass'])? $this->post['pass'] : '' )
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

    ->hr('<input type="hidden" autocomplete="off" value="1" name="path_admin">' )

    ->submit()
        ->addClass('btn btn-blue btn-large')
        ->value('เข้าสู่ระบบ');

?>
<div class="bgs">
    <div class="bg" style="background-image: url(<?=IMAGES?>carousel/c10-1.jpg);display: block;"></div>
</div>

<div class="section">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <img src="<?=IMAGES?>bgs/icon-login.png">
            <h2 class="text"><?=PAGE_TITLE?></h2>
            <div class="subtext"><?=PAGE_ADDRESS?></div>
        </div>

        <div class="login-container-wrapper">

            <div class="login-container">
                
                <div class="login-title">ลงชื่อเข้าใช้</div>

                <?=$form->html()?>
            
            </div>

        </div>
    </div>
<!-- /content-wrapper -->

<div class="bg-bubbles-wrapper hidden_elem">
<ul class="bg-bubbles">
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
</ul>
</div>

</div>
<!-- /section -->

