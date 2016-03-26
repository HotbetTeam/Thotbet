<?php

// require WWW_VIEW."employees/signup/initSteps.php";
require_once WWW_VIEW."employees/resume/sections/basic.php";
require_once WWW_VIEW."employees/resume/sections/insurance.php";
require_once WWW_VIEW."employees/resume/sections/experiences.php";
require_once WWW_VIEW."employees/resume/sections/education.php";
require_once WWW_VIEW."employees/resume/sections/contact.php";
require_once WWW_VIEW."employees/resume/sections/persons.php";
require_once WWW_VIEW."employees/resume/sections/docs.php";

?>

<div class="profile-resume">

	<?php if( !empty($this->item['mark_working']) ){ ?> 

	<section class="mbl">
		<div class="clearfix">
			<h2 class="title">จุดทำงานปัจจุบัน</h2>
			<!-- <a data-plugins="dialog" href="" class="btn-icon btn-edit"><i class="icon-pencil"></i></a> -->
		</div>
		
		<table cellspacing="0"><tbody>
			<tr>
				<td class="label">บริษัท</td>
				<td class="data"><?=$this->item['mark_working']['company_name']?></td>
			</tr>
			<tr>
				<td class="label">อัตราค่าจ้างรายวัน</td>
				<td class="data"><?= round( $this->item['mark_working']['uni_price'], 2 ) ?> <span class="fcg">บาท</span></td>
			</tr>
			<tr>
				<td class="label">อัตราค่าจ้าง OT</td>
				<td class="data"><?= round( $this->item['mark_working']['rate_per_OT'], 2 )?> <span class="fcg">บาท/ชม.</span></td>
			</tr>
			<tr>
				<td class="label">สถานะการรับเงินเป็น</td>
				<td class="data"><?php

					switch ($this->item['mark_working']['salaries_status']) {
						case 'W':
							echo 'วีค';
							break;
						
						default:
							echo 'เดือน';
							break;
					}

				  ?></td>
			</tr>
		</tbody></table>
		
	</section>

	<?php } ?>

	<?php foreach ($sections as $key => $value) {

	 ?>
		<section class="mbl" class="<?=$value['key']?>">
			<div class="clearfix">
				<h2 class="title"><?=$value['title']?></h2><?php

				if( $this->access['edit'] && $value['key']!='docs' ){ 
					$edit_url = !empty($value['edit_url'])
						? $value['edit_url']
						: URL.'employees/edit/'.$this->item['emp_id'].'/'.$value['key'];

				?>
				<a data-plugins="dialog" href="<?=$edit_url?>" class="btn-icon btn-edit"><i class="icon-pencil"></i></a>
				<?php } ?>

			</div>
			
			<table cellspacing="0"><tbody>
				<?php foreach ($value['lists'] as $i => $row) { ?>
					<tr>
						<td class="label"><?=$row['label']?></td>
						<td class="data"><?php

						if( $value['key']=='docs' ){
							echo '<a href="'.$row['data'].'" target="_blank">ดาวน์โหลด</a>';
						}
						else{
							echo $row['data'];
						}

						?></td>
					</tr>
				<?php } ?>
			</tbody></table>
		</section>
	<?php } ?>
</div>