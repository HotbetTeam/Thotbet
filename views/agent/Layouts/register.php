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
        ->addClass('btn btn-green btn-submit btn-large')
        
        ->value('Register')

    ->button()
        ->addClass('btn btn-link btn-large fsm or')
        ->attr('href', URL.'agent/login')
        ->value('Or Login');

?>

<div class="bgs o">
    <div class="bg" style="background-image: url(<?=IMAGES?>carousel/c10.jpg);display: block;"></div>
</div>

<div class="section y3">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <div class="text">
                <h2 >Agent - <span><?=PAGE_TITLE?></span><a href="<?=URL?>agent" style="color:#fff"><i class="icon-home mrs"></i>Home</a></h2>
            </div>
            <div class="subtext"><?=PAGE_ADDRESS?></div>
        </div>

        <div class="login-container-wrapper">

            <div class="login-container">
                
                <div class="login-title">Register Agent</div>

                <?=$form->html()?>
            
            </div>

        </div>
    </div>
<!-- /content-wrapper -->

</div>
<!-- /section -->

