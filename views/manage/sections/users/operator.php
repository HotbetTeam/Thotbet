<div class="SettingCol" data-plugins="SettingCol">
	<header class="SettingCol_header" data-elem="header">
		<div class="settings-content-title clearfix">
			<div class="rfloat settings-content-title-actions">
				<a data-plugins="dialog" href="<?=URL?>users/add?access_id=<?=$this->access_id?>" class="btn btn-primary"><i class="icon-plus mrs"></i><span>เพิ่ม</span></a>
			</div>
			<div>Operator</div>
		</div>
		<div class="settings-content-description">เพิ่มและตรวจสอบการทำงานของ Operator</div>
	</header>

	<div data-elem="content">
	<section class="settings-content-section">
		<table class="settings-table admin"><tbody>
			<tr>
				<th class="name">Name</th>
				<!-- <th class="checkbox">Admin</th>
				<th class="checkbox">แก้ไข<span class="hlep mls" data-options="<?=$this->fn->stringify(array('text'=>'sss'))?>" data-plugins="tooltip">?</span></th>
				<th class="checkbox">ควบคุม</th> -->
				<th class="actions">Actions</th>

			</tr>

			<?php foreach ($this->results['lists'] as $key => $item) { ?>
			<tr>
				<td class="name">
					<a class="fwb fcb"><?=$item['name']?></a>
					<div class="fcg fsm ellipsis">
						<?= !empty($item['email']) ?  '<span class="mrs"><i class="icon-envelope mrs"></i>'.$item['email'].'</span>':'' ?>
						<?= !empty($item['phone_number']) ?  '<span class="mrs"><i class="icon-phone mrs"></i>'.$item['phone_number'].'</span>':'' ?>
					</div>
				</td>
				<td class="actions">
				<?php
					$actions = $this->ui->toggle()
					->title(array(
						'icon'=> 'ellipsis-v',
						'class'=>"btn-txt btn",
					))
					->position( 'right' )

					->option('เปลี่ยนรหัสผ่าน')
						->link(URL."users/change_pass/{$item['user_id']}")
						->attr('data-plugins', 'dialog')

					/*->option('แก้ไขข้อมูล')
						->link(URL."users/form/{$item['user_id']}")
						->attr('data-plugins', 'dialog')*/

					->divider()
						
					->option('ลบ')
						->link( URL."users/del/{$item['user_id']}")
						->attr('data-plugins', 'dialog');
				?>

					<span class="group-btn" style="width:95px;">
						<a data-plugins="dialog" href="<?=URL?>users/edit/<?=$item['user_id'];?>?access_id=<?=$this->access_id?>" class="btn">แก้ไข</a>
						<?=$actions->getPluginJquey();?>
					</span>
					
				</td>

			</tr>
			<?php } ?>
		</tbody>
	</section>
	</div>
</div>