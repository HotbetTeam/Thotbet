<div style="position: absolute;right: 5px;top: 5px;"><?php
	$actions = $this->ui->toggle()
		->title(array(
			// 'text'=> '<i class="icon-"></i>',
			'class'=>"btn-txt btn-icon fcw",
			'icon' => 'ellipsis-v'
		))
		->position( 'right' )

		->option('แก้ไข')
			->link( URL."partner/edit/{$this->item['partner_id']}")
			->attr('data-plugins', 'dialog')

		->option('เปลี่ยนรหัสผ่าน')
			->link( URL."partner/change_pass/{$this->item['partner_id']}")
			->attr('data-plugins', 'dialog')

		->divider()

		->option('ลบ')
			->link( URL."partner/del/{$this->item['partner_id']}")
			->attr('data-plugins', 'dialog');

	echo $actions->getPluginJquey();

?></div>