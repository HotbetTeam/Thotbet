<?php


// $title[] = array('key'=>'check-box', 'text'=> '<label class="checkbox"><input id="checkboxes" type="checkbox"></label>' );
// $title[] = array('key'=>'star', 'text'=>'');

$title[] = array('key'=>'date', 'text'=>'เป็นสมาชิกเมื่อ', 'sort' => 'agent_created');
// $title[] = array('key'=>'image', 'text'=>'');
$title[] = array('key'=>'name', 'text'=>'ชื่อ', 'sort' => 'agent_name');

$title[] = array('key'=>'status', 'text'=>'จำนวนสมาชิก', 'sort' => 'agent_total_member');
$title[] = array('key'=>'date', 'text'=>'แก้ไขล่าสุด', 'sort' => 'agent_updated');

$title[] = array('key'=>'actions', 'text'=>'');

$this->tabletitle = $title;
$this->getURL =  URL.'manage/agent/';