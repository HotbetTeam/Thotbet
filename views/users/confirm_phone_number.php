<?php

// 
$FBoptions = array(
    'url'=>URL."login/facebook",
    'app_id'=>FACEBOOK_APP_ID,
    "active" => 'login'
);


$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('form-large')
    ->method('post')
    ->url(URL.'signup')

    // set field
    ->field("phone_number")
        ->label('หมายเลขโทรศัพท์')
        // ->placeholder("ชื่อ")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['phone_number'])? $this->post['phone_number'] : '' )
        ->notify( !empty($this->error['phone_number']) ? $this->error['phone_number'] : '' )

// UserSignUpIdvChallenge
    /*->field("format")
        ->text(
            'เราควรส่งรหัสให้คุณอย่างไร'.
            '<label class="radio"><input name="confirm_format" type="radio" value="SMS">ข้อความ (SMS)</label>'.
            '<label class="radio"><input name="confirm_format" type="radio" value="call">โทรศัพท์</label>'
        )*/
    // เราควรส่งรหัสให้คุณอย่างไร

    ->hr( !empty($this->next)
        ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
        : '' 
    )

    ->submit()
        ->addClass('btn btn-yellow btn-submit btn-login')
        ->value('ทำต่อ');


?><section class="wrapper clearfix login-1" id="login-1">
    <div class="signInForm signin-frame">
        <header class="pbm">
            <h1>กรอกหมายเลขเบอร์โทรศัพท์ของคุณ</h1></a>
            <p>ใกล้เสร็จแล้ว! เราเพียงต้องการยืนยันข้อมูลสมาชิกของคุณก่อนที่คุณจะสามารถใช้งานได้</p>
        </header>

        <div class="clearfix">
            <div class="sign-in-content">
                <?=$form->html()?>

                <!-- .. จะใช้หมายเลขนี้เพื่อการรักษาความปลอดภัยบัญชีเท่านั้น
อาจมีการคิดค่าบริการมาตรฐานสำหรับการส่งข้อความ -->

                <!-- <div>สำคัญ: .. จะไม่แชร์หมายเลขโทรศัพท์ของคุณกับบริษัทอื่นๆ หรือใช้สำหรับจุดประสงค์ทางการตลาดโดยเด็ดขาด</div> -->
            </div>
            
        </div>
    </div>

</section>