<table><tbody>		
	<?php if( empty($this->data['lists']) ){ ?>

	<tr>
		<td class="empty" colspan="10">
			<div class="empty-icon"><i class="icon-users"></i></div>
			<div class="empty-title">ไม่พบข้อมูล</div>
			<!-- <div class="empty-message">กรุณาเพิ่มข้อมูล</div> -->
		</td>
	</tr>

	<?php }else{ foreach ($this->data['lists'] as $i => $item) { ?>

	<tr data-id="<?=$item['user_id']?>">
		
		<td class="name"><div><a class="fwb" href="<?=$item['url']?>"><?=$item['name']?></a></div></td>
		<td class="status tws tac"><?=$item['access_name']?></td>

		<td class="date"><?php

		$timestamp = strtotime($item['updated']);
		$theTime = date("H.s", $timestamp);

		echo '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div><span class="fcg">'.$theTime.'น.</span>';

		?></td>
		<td class="actions tar"><?php

			$actions = $this->ui->toggle()
				->title(array(
					'text'=> '<i class="icon-cog"></i>',
					'class'=>"btn-txt btn",
				))
				->position( 'right' )

				->option('เปลี่ยนรหัสผ่าน')
					->link(URL."users/password/{$item['user_id']}")
					->attr('data-plugins', 'dialog')

				->option('แก้ไขข้อมูล')
					->link(URL."users/form/{$item['user_id']}")
					->attr('data-plugins', 'dialog')

				->divider()
					
				->option('ลบ')
					->link( URL."users/del/{$item['user_id']}")
					->attr('data-plugins', 'dialog');

			echo $actions->getPluginJquey();

		?></td>
	</tr>
	<?php } } ?>
</tbody></table>