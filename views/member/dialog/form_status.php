<?php

switch ($this->status) {
	case 'pause':
		$this->status_str = 'หยุดการใช้งาน';
		break;
	
	default:
		$this->status_str = 'เปิดการใช้งาน';
		break;
}


$arr['title'] = 'ยืนยันการเข้าใช้งานของสมาชิก'; // ยหยุด
$arr['body'] = "คุณต้องการ {$this->status_str} ข้อมูลสมาชิกที่ชื่อ <span class=\"fwb\">\"{$this->item['name']}\"</span> หรือไม่?";
$arr['form'] = '<form class="js-submit-form" action="'.URL.'member/status"></form>';

$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['user_id']);
$arr['hiddenInput'][] = array('name'=>'status','value'=>$this->status);

$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">ยืนยัน</span></button>';

echo json_encode($arr);