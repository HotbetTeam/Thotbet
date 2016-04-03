<div id="header-3">
	<div class="wrapper clearfix">
		<div class="lfloat">
			<ul class="nav">

				<?php 
				//date('T') . "" . str_replace(0, '', $GMT[0])
				$GMT = explode(":", date('P') );
    			?>
				<li class="top-clock" data-plugins="liveclock">
					<span class="top-clock-date" data-date-text></span>
					<span class="top-clock-text" data-clock-text></span>
					<!-- <span data-timezone class="mls"></span> -->
				</li>
				<!-- <li><i class="icon-comments-o mrs"></i><a class="link js-live-chat" href="#">Live Chat</a></li> -->
			</ul>
		</div>
		<?php if(!empty($this->me['m_id'])){ ?>
		<div class="rfloat">
			<ul class="nav">
				<!-- <li><i class="icon-question-circle mrs"></i><a class="link" href="#">ช่วยเหลือ</a></li> -->
				<li>ยินดีต้อนรับคุณ <a class="link" href="<?=URL?>settings"><?=$this->me['name']?></a></li>
				<?php if( isset($this->me['point_show']) ){ ?><li>แต้มสะสม <?=number_format($this->me['point_show'])?></li><?php } ?>
				<li><a onclick="return confirm('คุณต้องการที่จะออกระบบหรือไม่?');" href="<?=URL?>logout">ออกจากระบบ</a></li>
			</ul>
		</div>
		<?php }else{ ?>
			<div class="rfloat">
				<form class="top-login" action="<?=URL?>login" method="post">
					<table cellspacing="0">
						<tbody>
							<tr>
								<td>อีเมลหรือโทรศัพท์</td>
								<td>รหัสผ่าน</td>
								<td></td>
								<td class="sociallist">หรือเชื่อมต่อกับ:</td>
							</tr>
							<tr>
								<td><input id="email" placeholder="โทรศัพท์ หรืออีเมล์" class="inputtext" required="1" autocomplete="off" type="text" name="email"></td>
								<td><input id="pass" type="password" required="1" class="inputtext" placeholder="รหัสผ่าน" name="pass"></td>
								<td><button class="btn btn-yellow">เข้าสู่ระบบ</button></td>
								<td class="sociallist">
									<a class="btn btn-facebook" type="button" onclick="$('button[data-provider=facebook]').click()"><i class="icon-facebook"></i><p>Facebook</p></a>
									<a class="btn btn-twitter" type="button" onclick="$('button[data-provider=twitter]').click()"><i class="icon-twitter"></i><p>Facebook</p></a>
									<a class="btn btn-google" type="button" onclick="$('button[data-provider=google]').click()"><i class="icon-google"></i><p>Facebook</p></a>
								</td>
							</tr>
							<tr>
								<td colspan="4"><a class="forgot" href="<?=URL?>forgotpassword">ลืมรหัสผ่าน?</a> หรือ<a class="switch-link" href="<?=URL?>register">สร้างบัญชีใหม่</a></td>
							</tr>
						</tbody>
					</table>
					
				</form>
			</div>
		<?php } ?>
	</div>
</div>



<div id="global-nav">
	<div class="wrapper clearfix">
		<h1 id="logo"></h1>
		<!-- <div class="lfloat">
			<ul class="nav">
				<li><a>Help</a></li>
			</ul>
		</div> -->
		
		<div class="rfloat">
			<ul id="page-nav" class="page-nav clearfix"><?php

			$pagenav[] = array('key'=>'home', 'text'=>'Home', 'url'=>URL);
			$pagenav[] = array('key'=>'casinoOnline', 'text'=>'Casino Online', 'url'=>URL.'casino/online');
			$pagenav[] = array('key'=>'sportOnline', 'text'=>'Sport Online', 'url'=>URL.'sport/online');
			$pagenav[] = array('key'=>'slot', 'text'=>'Slot Machine', 'url'=>URL.'slot');
			$pagenav[] = array('key'=>'promotion', 'text'=>'Promotion', 'url'=>URL.'promotion');
			$pagenav[] = array('key'=>'partner', 'text'=>'Partner', 'url'=>URL.'partner');
			$pagenav[] = array('key'=>'join', 'text'=>'Join us', 'url'=>URL.'join');

			/*$pagenav[] = array('key'=>'home', 'text'=>'Casino', 'url'=>URL);
			$pagenav[] = array('key'=>'live-Casino', 'text'=>'Live Casino', 'url'=>'');
			$pagenav[] = array('key'=>'promotion', 'text'=>'Promotion', 'url'=>'');
			$pagenav[] = array('key'=>'mobile', 'text'=>'Mobile', 'url'=>'');
			$pagenav[] = array('key'=>'help', 'text'=>'Help', 'url'=>'');*/

			foreach ($pagenav as $key => $value) {

				$current = $this->currentPage==$value['key']? ' class="current"':'';

				echo '<li'.$current.'><a'.(!empty($value['url'])? ' href="'.$value['url'].'"':'').'>'.$value['text'].'</a></li>';
			}

			?>
			</ul>
		</div>
	</div>

</div>