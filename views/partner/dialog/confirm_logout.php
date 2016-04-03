<?php

$arr['title'] = 'ยืนยันการออกจากระบบ';
$arr['body'] = "คุณต้องการออกจากระบบหรือไม่?";
// $arr['form'] = '<form class="js-submit-form" action="'.URL.'agent/del"></form>';

$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
$arr['button'] .= '<a href="'.URL.'logout/partner" class="btn btn-blue"><span class="btn-text">ออกจากระบบ</span></a>';

echo json_encode($arr);