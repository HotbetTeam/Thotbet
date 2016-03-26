<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container standard form-insert form-large')
    ->method('post')
    ->url(URL.'login')

    // set field
    ->field("email")
        ->placeholder("โทรศัพท์ หรืออีเมล์")
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
        ->notify( !empty($this->error['pass']) ? $this->error['pass'] : '' )

    ->hr( !empty($this->next)
            ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
            : '' 
    )
    ->field('remember-forgot')
        ->text( '<label class="remember checkbox hidden_elem">
        <input type="checkbox" value="1" name="remember_me" checked="checked">
        <span>จํารหัสผ่าน</span>
      </label><span class="separator mhs hidden_elem">·</span><a class="forgot" href="'.URL.'forgotpassword">ลืมรหัสผ่าน?</a>' )

    ->button()
        ->addClass('btn btn-facebook')
        ->attr('data-plugins' , 'social')
        ->attr('data-options', $this->fn->stringify(array(
                'network' => 'facebook',
                // 'redirect'=>URL."auth/facebook",
                'client_id' => FACEBOOK_APP_ID,
                "active" => 'login',
                'url' => URL."login/social",
            )) )
        ->value('<i class="icon-facebook"></i>')

    ->button()
        ->addClass('btn btn-twitter')
         ->attr('data-plugins' , 'social')
        ->attr('data-options', $this->fn->stringify(array(
                'network' => 'twitter',
                'client_id'=> GOOGLE_CLIENT_ID,
                'client_secret'=> GOOGLE_CLIENT_SECRET,
                'scopes' => GOOGLE_SCOPES,
                'redirect'=> URL."auth/twitter",
                "active" => 'login',
                'url' => URL."login/social",
            )) )

        ->value('<i class="icon-twitter"></i>')

    ->button()
        ->addClass('btn btn-google')
        ->attr('data-plugins' , 'social')
        ->attr('data-options', $this->fn->stringify(array(
                'network' => 'google',
                'redirect'=> URL."auth/google",
                'client_id'=> GOOGLE_CLIENT_ID,
                'client_secret'=> GOOGLE_CLIENT_SECRET,
                'scopes' => GOOGLE_SCOPES,
                "active" => 'login',
                'url' => URL."login/social",
            )) )
        ->value('<i class="icon-google-plus"></i>')

    ->submit()
        ->addClass('btn btn-yellow btn-submit btn-login')
        ->value('เข้าสู่ระบบ');

?>
<div class="page-body">
    <div class="wrapper">
    <div class="home-news">
        <a class="home-news-link"><i class="icon-bullhorn mrs"></i></a>
        <div class="lists-news-wrapper">
            <div data-plugins="textslide">
            <ul class="lists-news">
                <li><span>ร่วมสนุกกับ</span></li>
                <li><a>Gclub</a></li>
                <li><a>Holiday Palace</a></li>
                <li><a>Genting CrownRoyal 1688</a></li>
                <li><a>Ruby 888</a></li>
                <li><span>คาสิโนออนไลน์ มั่นใจไปกับเรา ทั้งบริการและความซื่อตรงที่เหนือใครบริการ casino online ตลอด 24 ชม.</span></li>
            </ul>
            </div>
        </div>

        <!-- <a class="prevnext prev btn"><i class="icon-chevron-up"></i></a> -->
        <!-- <a class="prevnext next btn"><i class="icon-chevron-down"></i></a> -->
    </div>

    <div class="game-sub-item">
        <ul class="clearfix">
            <li class="item">
                <div>
                    <figure><img src="<?=IMAGES?>games/sport.png"></figure>
                    <a href="<?=URL?>sport/online" class="btn game-sub-link">Sport</a>
                </div>
            </li>
            <li class="item">
                <figure><img src="<?=IMAGES?>games/live_dealer.png"></figure>
                
                <a href="<?=URL?>casino/online" class="btn game-sub-link">Casino</a>
            </li>
            <li class="item">
                <figure><img src="<?=IMAGES?>games/casino.png"></figure>
                
                <a href="<?=URL?>slot" class="btn game-sub-link">Slots</a>
            </li>

            <?php if( empty($this->me) ){ ?>
            <li class="login">
                <header class="login-register-header clearfix">
                    <div class="login-header lfloat">ลงชื่อเข้าใช้งาน</div>
                    <div class="login-switch rfloat">หรือ <a class="switch-link" href="<?=URL?>register">สร้างบัญชี</a></div>
                </header>
                
                <?=$form->html()?>
            </li>
            <?php } else{ ?>


            <?php if( isset($this->me['point_show']) ){ ?>
            <li class="min-profile-member">

                <header>
                    <h2>ประวัติส่วนตัว</h2><a class="mls" href="<?=URL?>settings">แก้ไข</a>
                </header>
                
                <table><tbody>
                    <tr>
                        <td class="label">ชื่อ</td>
                        <td class="data"><?=$this->me['name']?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="label" style="padding-top:16px;">ข้อมูลสำหรับการเข้าเล่นเกม</td>
                        <!-- <td colspan="2" class="label" style="padding-top:16px;">ข้อมูลสำหรับการเข้าเล่นเกม</td> -->
                    </tr>
                    <tr>
                        <td class="label">ชื่อผู้เข้าใช้: </td>
                        <td class="data"><?=$this->me['game_user']?></td>
                    </tr>
                    <tr>
                        <td class="label">รหัสผ่าน: </td>
                        <td class="data"><?=$this->me['game_pass']?></td>
                    </tr>
                </tbody></table>
            </li>

                <?php } // number_format( round( $this->me['point_show'], 2, PHP_ROUND_HALF_DOWN ) ) ?>

            <?php } ?>
        </ul>
    </div>
    </div>
</div>