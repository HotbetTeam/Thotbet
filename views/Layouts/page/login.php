<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container form-insert form-large')
    ->method('post')
    ->url(URL.'manage')

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

    ->hr('<input type="hidden" autocomplete="off" value="1" name="path_admin">' )

    ->submit()
        ->addClass('btn btn-blue btn-large')
        ->value('เข้าสู่ระบบ');

?>

<div class="bgs">
    <div class="bg" style="background-image: url(<?=IMAGES?>carousel/c4.jpg);display: block;"></div>
</div>

<div class="section">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <div class="text">
                <h2 >Login To Admin<a href="<?=URL?>"><i class="icon-home mrs"></i>หน้าแรก</a></h2>
            </div>
            <div class="subtext mvm"><span><?=PAGE_TITLE?></span></div>
        </div>

        <div class="login-container-wrapper">

            <div class="login-container">
                
                <div class="login-title">Login</div>

                <?=$form->html()?>
            
            </div>

        </div>
    </div>
<!-- /content-wrapper -->

</div>
<!-- /section -->

