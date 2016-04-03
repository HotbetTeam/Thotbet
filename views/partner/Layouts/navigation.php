<?php

function nav($lists=array(), $active=null){


	$li = '';
	foreach ($lists as $key => $value) {
		
		$selected = $active==$value['key'] ? ' class="active"':'';

		$icon = '';
		if( !empty($value['icon']) ){
			$icon = '<i class="icon-'.$value['icon'].'"></i>';
		}

		$li.='<li'.$selected.'><a href="'.$value['link'].'">'.$icon.$value['text'].'</a></li>';
	}

	return '<ul class="navigation-list">'.$li.'</ul>';
}


?>

<div class="navigation-main-bg navigation-trigger"></div>
<nav class="navigation-main" role="navigation">
	<a class="btn btn-icon navigation-trigger"><i class="icon-bars"></i></a>
	<div class="navigation-main-content">

<div class="welcome-box"><div class="anchor clearfix"><div class="avatar lfloat"><img class="img" src="<?=$this->me['image_url']?>" alt="<?=$this->me['partner_name']?>"></div><div class="content"><div class="spacer"></div><div class="massages"><div class="fullname"><?=$this->me['partner_name']?></div><span class="subname">จำนวนสมาชิก <?=$this->me['partner_total_member']?></span></div></div></div></div>

<?php

$frist[] = array('key'=>'back_to_home','text'=>'กลับไปยังเว็บไซต์หลัก','link'=>URL,'icon'=>'reply-all');
echo nav($frist, $this->currentPage);


echo '<div class="navigation-btn-add"><a href="'.URL.'partner/member/add" data-plugins="dialog" class="btn btn-red btn-border btn-large"><i class="icon-plus"></i><span class="mls btn-text">เพิ่มสมาชิก</span></a></div>';
#pinnedNav
$pinned[] = array('key'=>'banner','text'=>'สร้างแบนเนอร์','link'=>URL.'partner/banner','icon'=>'picture-o');
$pinned[] = array('key'=>'member','text'=>'สมาชิก','link'=>URL.'partner/member','icon'=>'users');
echo '<h4 class="navigation-header">จัดการ</h4>';
echo nav($pinned, $this->currentPage);


$a[] = array('key'=>'settings','text'=>'ตั้งค่า','link'=>URL.'partner/settings','icon'=>'cog');
echo nav($a, $this->currentPage);


?>
    
	</div>

	<div class="navigation-main-footer">

<?php


// $footer[] = array('key'=>'logout','text'=>'ออกจากระบบ','link'=>URL.'logout/partner','icon'=>'power-off');
// echo nav($footer, $this->currentPage);
echo '<ul class="navigation-list"><li><a data-plugins="dialog" href="'.URL.'logout/partner"><i class="icon-power-off"></i>ออกจากระบบ</a></li></ul>';
?>
	</div>
</nav>