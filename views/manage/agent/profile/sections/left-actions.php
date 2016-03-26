<div style="position: absolute;right: 5px;top: 5px;"><?php
	$actions = $this->ui->toggle()
		->title(array(
			// 'text'=> '<i class="icon-"></i>',
			'class'=>"btn-txt btn-icon fcw",
			'icon' => 'ellipsis-v'
		))
		->position( 'right' )

		->option('แก้ไข')
			->link( URL."agent/edit/{$this->item['agent_id']}")
			->attr('data-plugins', 'dialog')

		->option('เปลี่ยนรหัสผ่าน')
			->link( URL."agent/change_pass/{$this->item['agent_id']}")
			->attr('data-plugins', 'dialog')

		->divider()

		->option('ลบ')
			->link( URL."agent/del/{$this->item['agent_id']}")
			->attr('data-plugins', 'dialog');

	echo $actions->getPluginJquey();

?></div>