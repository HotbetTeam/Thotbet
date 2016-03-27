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

<div class="welcome-box"><div class="anchor clearfix"><div class="avatar lfloat"><img class="img" src="<?=$this->me['image_url']?>" alt="<?=$this->me['agent_name']?>"></div><div class="content"><div class="spacer"></div><div class="massages"><div class="fullname"><?=$this->me['name']?></div><span class="subname"></span></div></div></div></div>

<?php

$frist[] = array('key'=>'back_to_home','text'=>'กลับไปยังเว็บไซต์หลัก','link'=>URL,'icon'=>'reply-all');
echo nav($frist, $this->currentPage);

echo '<div class="navigation-btn-add"><a href="'.URL.'member/add" data-plugins="dialog" class="btn btn-red btn-border btn-large"><i class="icon-plus"></i><span class="mls btn-text">เพิ่มสมาชิกใหม่</span></a></div>';
#pinnedNav
// $pinned[] = array('key'=>'topics','text'=>'ข่าวสาร','link'=>URL.'admin/topics','icon'=>'newspaper-o');
$pinned[] = array('key'=>'agent','text'=>'Agent','link'=>URL.'manage/agent','icon'=>'user-secret');
$pinned[] = array('key'=>'member','text'=>'สมาชิก','link'=>URL.'member','icon'=>'users');

// $pinned[] = array('key'=>'inbox','text'=>'Live Chat','link'=>URL.'inbox','icon'=>'comments-o');
// $pinned[] = array('key'=>'operator','text'=>'Operator','link'=>URL.'operator','icon'=>'headphones');

echo '<h4 class="navigation-header">จัดการสมาชิก</h4>';
// $pinned[] = array('key'=>'users','text'=>'ผู้ดูแลระบบ','link'=>URL.'users','icon'=>'check-square-o');
echo nav($pinned, $this->currentPage);

#play
$play[] = array('key'=>'playing','text'=>'Playing','link'=>URL.'playing','icon'=>'play');
echo '<h4 class="navigation-header">การเล่นเกม</h4>';
echo nav($play, $this->currentPage);


#manage
$a[] = array('key'=>'manage','text'=>'ตั้งค่า','link'=>URL.'manage','icon'=>'cog');
echo nav($a, $this->currentPage);

// echo nav($a);

// $cog[] = array('key'=>'overview','text'=>'ตั้งค่า','link'=>URL);
// echo nav($cog);
?>
    
	</div>

	<div class="navigation-main-footer">

<?php


$footer[] = array('key'=>'manage_logout','text'=>'ออกจากระบบ','link'=>URL.'logout/admin','icon'=>'power-off');
// $footer[] = array('key'=>'manage_settings','text'=>'ตั้งค่า','link'=>URL.'manage/settings','icon'=>'cog');

echo nav($footer, $this->currentPage);
?>
	</div>
</nav>