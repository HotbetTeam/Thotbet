<?php

$arr['title'] = 'ยืนยันการลบข้อมูล';

if( $this->item['membership']>0 ){
	$arr['body'] = "คุณไม่สามารถลบข้อมูลของระดับสมาชิก <span class=\"fwb\">\"ระดับ {$this->item['lev_name']}\"</span> เนื่องจากมีสมาชิกที่อยู่ในระดับนี้อยู่";
	$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ปิด</span></a>';

}
else{
	$arr['body'] = "คุณต้องการลบข้อมูลของระดับสมาชิก <span class=\"fwb\">\"ระดับ {$this->item['lev_name']}\"</span> หรือไม่?";
	$arr['form'] = '<form class="js-submit-form" action="'.URL.'member/form_level_del"></form>';
	$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['lev_id']);
	$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';

	$arr['button'] .= '<button type="submit" class="btn btn-link btn-submit"><span class="btn-text">ลบ</span></button>';

}

echo json_encode($arr);