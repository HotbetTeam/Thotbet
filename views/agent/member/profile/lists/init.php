<?php


$title[] = array('key'=>'date', 'text'=>'Date', 'sort'=>'pl_date');

$title[] = array('key'=>'number', 'text'=>'Wagers', 'sort'=>'pl_wagers');
$title[] = array('key'=>'number', 'text'=>'Bet amount', 'sort'=>'pl_bet_amount');

$title[] = array('key'=>'number', 'text'=>'Member', 'sort'=>'pl_menber');
$title[] = array('key'=>'number point', 'text'=>'Point', 'sort'=>'pl_menber');

$title[] = array('key'=>'number', 'text'=>'Actual betting Qty', 'sort'=>'pl_actual');
$title[] = array('key'=>'number point', 'text'=>'Point', 'sort'=>'pl_actual');

$title[] = array('key'=>'number', 'text'=>'Sum Point', 'sort'=>'m_point');

// $title[] = array('key'=>'action', 'text'=>'');

$this->tabletitle = $title;
$this->getURL =  URL.'agent/member/'.$this->item['m_id'];