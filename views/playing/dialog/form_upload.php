<?php

$f = new Form();
$form = $f->create()
	
	// set From
	->elem('div')
	->addClass('form-insert')

		->field('date')
		    ->label('วันที่')
		    ->addClass('inputtext')
		    ->attr('data-plugins', 'datepicker')
		    ->attr('data-options', $this->fn->stringify(array('style'=>'normal')) )
		       
		->field('file')
		    ->label('เลือกไฟล์จากคอมพิวเตอร์ของคุณ')
		    ->text( '<div class="clearfix" data-plugins="chooseFile">
					<div class="choose-file clearfix"><a class="btn btn-choosefile lfloat mrm"><span class="btn-text">เลือกไฟล์</span><input role="choosefile" type="file" name="file1" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /></a>
					<div class="text ellipsis lfloat mrs" data-name="ไม่ได้เลือกไฟล์"></div>
					<a class="btn-icon close hidden_elem" data-remove type="button"><i class="icon-remove"></i></a>
					</div>
					<div class="mts fcg">(ประเภทไฟล์ Excel และขนาดสูงสุด 25 MB)</div>
				</div>' );
 
// body
$arr['body'] = $form->html(); 

// title
$arr['title'] = 'อัพโหลดข้อมูลการเล่น';

// 
$arr['form'] = '<form class="js-submit-form" data-plugins="changeForm" method="post" action="'.URL.'playing/upload"></form>';

// fotter
// $arr['bottom_msg'] = '<div class="lfloat mts fcr js-upload-file-error"></div>';
$arr['button'] = '<button class="btn btn-blue btn-submit disabled" type="submit"><span class="btn-text">อัพโหลด</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';

echo json_encode($arr);