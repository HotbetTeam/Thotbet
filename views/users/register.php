<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('form-large')
    ->method('post')
    ->url(URL.'register')

    // set field
    ->field("name")
        ->label('ชื่อ')
        // ->placeholder("ชื่อ")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['name'])? $this->post['name'] : '' )
        ->notify( !empty($this->error['name']) ? $this->error['name'] : '' )

    ->field("email")
    	->label('โทรศัพท์หรืออีเมล์*')
        // ->placeholder("โทรศัพท์หรืออีเมล์")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['email'])? $this->post['email'] : '' )
        ->notify( !empty($this->error['email']) ? $this->error['email'] : '' )

    ->field("pass")
    	->label('รหัสผ่าน*')
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        // ->placeholder("รหัสผ่าน")
        // ->value( !empty($this->post['password'])? $this->post['password'] : '' )
        ->notify( !empty($this->error['pass']) ? $this->error['pass'] : '' )

    ->hr( !empty($this->next)
            ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
            : '' 
    )

    ->submit()
        ->addClass('btn btn-yellow btn-submit btn-login')
        ->value('สมัครใช้งาน');


?><section class="wrapper clearfix login-1" id="login-1">
    <div class="signInForm">
        <header><h1>สมัครใช้งาน</h1> หรือ <a href="<?=URL?>login">เข้าสู่ระบบ</a> </header>

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