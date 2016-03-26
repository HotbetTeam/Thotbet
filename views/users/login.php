<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('form-large')
    ->method('post')
    ->url(URL.'login')

    // set field
    ->field("email")
    	->label('โทรศัพท์ หรืออีเมล์')
        // ->placeholder("โทรศัพท์ หรืออีเมล์")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['email'])? $this->post['email'] : '' )
        ->notify( !empty($this->error['email']) ? $this->error['email'] : '' )

    ->field("pass")
    	->label('รหัสผ่าน<a class="rfloat" href="'.URL.'forgotpassword">ลืมรหัสผ่าน?</a>')
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        // ->placeholder("รหัสผ่าน")
        ->value( !empty($this->post['pass'])? $this->post['pass'] : '' )
        ->notify( !empty($this->error['pass']) ? $this->error['pass'] : '' )

    ->hr( !empty($this->next)
            ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
            : '' 
    )
    /*->field('remember-forgot')
        ->text( '<label class="remember checkbox hidden_elem">
                <input type="checkbox" value="1" name="remember_me" checked="checked">
                <span>จํารหัสผ่าน</span>
              </label>
                <a class="forgot" href="'.URL.'forgotpassword">ลืมรหัสผ่าน?</a>' )*/

    ->submit()
        ->addClass('btn btn-yellow btn-submit btn-login')
        ->value('เข้าสู่ระบบ');


?><section class="wrapper clearfix login-1" id="login-1">
	<div class="signInForm">
		<header><h1>ลงชื่อเข้าใช้งาน</h1> หรือ <a href="<?=URL?>register">สมัคสมาชิก</a> </header>
        
        <div class="clearfix">
            <div class="sign-in-content">
                <?=$form->html()?>
            </div>
            <div class="secondary">
               <div class="mbm">หรือสามารถเชื่อมต่อกับ</div>

                <button data-plugins="social" data-options="<?=$this->fn->stringify( array(
                    'network' => 'facebook',
                    'client_id' => FACEBOOK_APP_ID,
                    "active" => 'login',
                    'url' => URL."login/social",
                ) )?>" class="btn btn-facebook" data-provider="facebook" type="button"><i class="icon-facebook"></i><p>Facebook</p></button>
                <button data-plugins="social" data-options="<?=$this->fn->stringify( array(
                    'network' => 'twitter',
                    'client_id'=> GOOGLE_CLIENT_ID,
                    'client_secret'=> GOOGLE_CLIENT_SECRET,
                    'scopes' => GOOGLE_SCOPES,
                    'redirect'=> URL."auth/twitter",
                    "active" => 'login',
                    'url' => URL."login/social",
                ) )?>" class="btn btn-twitter" data-provider="twitter" type="button"><i class="icon-twitter"></i><p>Twitter</p></button>
                <button data-plugins="social" data-options="<?=$this->fn->stringify( array(
                    'network' => 'google',
                    'redirect'=> URL."auth/google",
                    'client_id'=> GOOGLE_CLIENT_ID,
                    'client_secret'=> GOOGLE_CLIENT_SECRET,
                    'scopes' => GOOGLE_SCOPES,
                    "active" => 'login',
                    'url' => URL."login/social",
                ) )?>" class="btn btn-google" data-provider="twitter" type="button"><i class="icon-google"></i><p>Google</p></button>
                
            </div>
        </div>
    </div>
</section>