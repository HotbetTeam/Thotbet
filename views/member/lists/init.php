<?php

$title[] = array('key'=>'date', 'text'=>'วันที่เพิ่ม', 'sort'=>'m_created');

if( $this->status!='verify' ){
	$title[] = array('key'=>'status', 'text'=>'สถานะ');
	$title[] = array('key'=>'username', 'text'=>'Member Account', 'sort'=>'m_username'); //, 'sort'=>'m.user'
}

$title[] = array('key'=>'name', 'text'=>'ชื่อ', 'sort'=>'m_name');


if( $this->status!='verify' ){
	$title[] = array('key'=>'status', 'text'=>'ระดับ', 'sort'=>'lev_score');
	$title[] = array('key'=>'number', 'text'=>'แต้มจริง', 'sort'=>'m_point');
	$title[] = array('key'=>'number', 'text'=>'แต้มแสดง', 'sort'=>'m_point_show');
}

$title[] = array('key' => 'text', 'text' => 'Agent', 'sort' => 'm_agent_id');
$title[] = array('key'=>'action', 'text'=>'');

$this->tabletitle = $title;
$this->getURL =  URL.'member';