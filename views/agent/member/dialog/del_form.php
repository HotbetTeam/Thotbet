<?php

$arr['title'] = 'ยืนยันการลบข้อมูล';
$arr['body'] = "คุณต้องการลบข้อมูลสมาชิก <span class=\"fwb\">\"{$this->item['name']}\"</span> หรือไม่?";
$arr['form'] = '<form class="js-submit-form" action="'.URL.'agent/member/del"></form>';
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['m_id']);
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-red btn-submit"><span class="btn-text">ลบ</span></button>';

echo json_encode($arr);