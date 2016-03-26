<div style="position: absolute;right: 5px;top: 5px;"><?php
	$actions = $this->ui->toggle()
		->title(array(
			// 'text'=> '<i class="icon-"></i>',
			'class'=>"btn-txt btn-icon fcw",
			'icon' => 'ellipsis-v'
		))
		->position( 'right' )

		->option('ข้อมูลการเล่นเกมส์')
			->link( URL."member/change_about_game/{$this->item['m_id']}")
			->attr('data-plugins', 'dialog')

		->divider()
		->option('เปลี่ยนรหัสผ่าน')
			->link(URL."member/change_pass/{$this->item['m_id']}")
			->attr('data-plugins', 'dialog')

		->divider()
		->option('หยุดการใช้งาน')
			->link(URL."member/change_status/{$this->item['m_id']}/pause")
			->attr('data-plugins', 'dialog')

		->divider()

		->option('ลบ')
			->link( URL."member/del/{$this->item['m_id']}")
			->attr('data-plugins', 'dialog');

	echo $actions->getPluginJquey();

?></div>