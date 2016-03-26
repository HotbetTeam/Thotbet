<div class="settings-content-title">ระดับสมาชิก</div>
<div class="settings-content-description">ปรับเปลี่ยนค่าระดับสมาชิก และตรวจสอบจำนวนสมาชิกที่อยู่ในระบบคะแนน</div>

<hr class="settings-content-hr">
<section class="settings-content-description">
	<strong>ขั้นตอนการคิดคะแนนระดับสมาชิก:</strong>
	<div>เมื่อคะแนนของสมาชิกถึงระดับที่กำหนด ระบบจะทำการปรับระดับให้อยู่ในระดับนั้นๆ	</div>
</section>

<section class="settings-content-section">
	
	<table class="settings-table"><tbody>
		<tr>
			<th class="text">ระดับ</th>
			<th class="score">คะแนน</th>
			<th class="checkbox">อัตโนมัติ</th>
			<th class="status">จำนวนสมาชิก</th>
			<th class="actions">Actions</th>
			<th></th>
		</tr>
		
		<?php foreach ($this->results['lists'] as $key => $item) { ?>
			<tr>
				<td class="text"><?=$item['lev_name']?></td>
				<td class="score"><?=$item['lev_score']?></td>
				<td class="checkbox"><?php

					echo $item['lev_has_auto']==true
						? '<i class="icon-check mrs"></i>'
						: '<i class="icon-remove mrs"></i>';

				?></td>
				<td class="status"><?=$item['membership']?></td>
				<td class="actions"><span class="group-btn">
					<a data-plugins="dialog" href="<?=URL?>member/form_level/<?=$item['lev_id']?>" class="btn">แก้ไข</a>
					
					<?php if($item['lev_id']!=1){ ?>
					<a data-plugins="dialog" href="<?=URL?>member/form_level_del/<?=$item['lev_id']?>" class="btn">ลบ</a>
					<?php } ?>
				</span></td>
				<td></td>
			</tr>
		<?php } ?>
		
		
	</tbody></table>
</section>

<section class="settings-content-section">
	<a data-plugins="dialog" href="<?=URL?>member/form_level" class="btn btn-primary">เพิ่มระดับใหม่</a>
</section>