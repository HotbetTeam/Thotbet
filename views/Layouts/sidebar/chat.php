<?php


$options = array(
	// 'me'=> $this->me,
	'username'=> !empty($this->me['m_id']) ? $this->me['m_id']:null,
	'key' => !empty($this->me['conversation_key_id']) ? $this->me['conversation_key_id']:null,
	'ROOT' => FIREBASE_SERVER,

	'ref'=> 'webMessenger',
	'URL'=> URL."messages/",
	// 'limit'=>50,
	'sound'=>true,
	'url_sound'=>URL.'public/sounds/message.mp3',
	
);

?>
<div class="chat" data-plugins="chat" data-options="<?=$this->fn->stringify($options)?>">
	
	<div class="form-chat-wrapper">
		<ul class="form-chat">
			<?php if( empty($this->me) ){ ?>
			<li class="login">
				
				<header class="chat-header clearfix">
					<div class="lfloat title">
						<strong class="name">ลงชื่อเข้าใช้งาน</strong>
					</div>
					<div class="rfloat"><a class="link link-close"><i class="icon-remove"></i><span>ปิด</span></a></div>
				</header>

				<div class="content clearfix">
					<div class="sociallist">
						<div>เชื่อมต่อกับ:</div>

						
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
		                ) )?>" class="btn btn-google" data-provider="google" type="button"><i class="icon-google"></i><p>Google</p></button>

					</div>
				
					<div class="or"></div>

					<div class="login-register-container login">
						<form action="<?=URL?>login" method="post" class="form login-part">
							<label class="input">
								<input class="inputtext" name="email" type="text" placeholder="โทรศัพท์หรืออีเมล์">
							</label>
							
							<label class="input">
								<input class="inputtext"  name="pass" type="password" placeholder="รหัสผ่าน">
							</label>
							
							<div class="clearfix">
								<button type="submit" class="btn btn-blue btn-submit">เข้าสู่ระบบ</button>
								<span> หรือ <a class="js-register-switch">สมัครสมาชิก</a></span>
							</div>
						</form>

						<form action="<?=URL?>register" method="post" class="form register-part">
							<label class="input">
								<input class="inputtext" name="name" type="text" placeholder="ชื่อ">
							</label>

							<label class="input">
								<input class="inputtext" name="email" type="text" placeholder="โทรศัพท์หรืออีเมล์">
							</label>
							
							<label class="input">
								<input class="inputtext"  name="pass" type="password" placeholder="รหัสผ่าน">
							</label>
							
							<div class="clearfix">
								<button type="submit" class="btn btn-blue btn-submit">สมัครสมาชิก</button>
								<span> หรือ <a class="js-register-switch">ลงชื่อเข้าใช้งาน</a></span>
							</div>
						</form>
					</div>	

				</div>
			</li>
			<?php }else{ ?>
			<li class="box-chat"></li>
			<?php } ?>
		</ul>
	</div>

	<div class="arrow">
		<div class="arrow-border"></div>
		<div class="arrow-body"></div>
	</div>

</div>

<?php

/*<!-- <li class="clearfix received">
	<div class="box">
		<div class="text">สวัสดีค่ะ เนื่องจากในขณะนี้มีผู้สอบถามเข้ามาเป็นจำนวนมาก กรุณารอสักครู่แล้วเราจะรีบตอบกลับนะคะ หรือคุณสามารถทิ้งข้อมูลติดต่อกลับ และรูปหน้าจอ (ในกรณีที่มีปัญหาขัดข้อง) ไว้ให้ฟ้าก็ได้ค่ะ - ขอบคุณค่ะ! :)</div>
		<div class="time">12:08</div>
	</div>
</li>

<div class="clearfix newday">
	<span> <span>วันนี้</span> </span> 
</div>

<li class="clearfix sent">
	<div class="box">
		<div class="text">You asked, Font Awesome delivers with 20 shiny new icons in version 4.5. Want to request new icons? Here's how. Need vectors or want to use on the desktop? Check the cheatsheet.</div>
		<div class="time">12:08</div>
	</div>
</li>

<li class="clearfix sent">
	<div class="box">
		<div class="text">You asked.</div>
		<div class="time">12:08</div>
	</div>
</li> -->*/