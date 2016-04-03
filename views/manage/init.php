<?php

$menu[] = array(
	'text' => 'Manage Users',
	'sub' => array( 0=>
		array(
			'text' => 'ผู้ดูแลระบบ',
			'key' => 'admin',
			'url' => URL.'manage/users/admin'
		),
		array(
			'text' => 'Operator',
			'key' => 'operator',
			'url' => URL.'manage/users/operator'
		)
	)
);

$menu[] = array(
	'text' => 'Member',
	'url' => URL.'manage/member/',
	'sub' => array( 0=>
		array(
			'text' => 'ระดับสมาชิก',
			'key' => 'level',
			'url' => URL.'manage/member/level'
		),
		array(
			'text' => 'การคำนวณแต้มสมาชิก',
			'key' => 'points',
			'url' => URL.'manage/member/points'
		)
	)
);

$menu[] = array(
	'text' => 'Partner',
	'sub' => array( 0=>
		array(
			'text' => 'Banner',
			'key' => 'banner',
			'url' => URL.'manage/partner/banner'
		)
	)
);