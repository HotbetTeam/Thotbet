<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container form-insert form-large js-submit-form')
    ->method('post')
    ->url(URL.'agent/login')

    // set field
    ->field("email")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->placeholder("Phone or Email")

    ->field("pass")
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        ->placeholder("Password");


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
        
        ->value('Login')

    ->button()
        ->addClass('btn btn-link btn-large fsm or')
        ->attr('href', URL.'agent/register')
        ->value('Or Register');

?>

<div class="bgs o">
    <div class="bg" style="background-image: url(<?=IMAGES?>carousel/c10.jpg);display: block;"></div>
</div>

<div class="section">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <div class="text">
                <h2 >Agent - <span><?=PAGE_TITLE?></span><a href="<?=URL?>agent" style="color:#fff"><i class="icon-home mrs"></i>Home</a></h2>
            </div>
            <div class="subtext"><?=PAGE_ADDRESS?></div>
        </div>

        <div class="login-container-wrapper">

            <div class="login-container">
                
                <div class="login-title">Login Agent</div>

                <?=$form->html()?>
            
            </div>

        </div>
    </div>
<!-- /content-wrapper -->

</div>
<!-- /section -->

