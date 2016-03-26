<div style="position: absolute;right: 5px;top: 5px;"><?php
	$actions = $this->ui->toggle()
		->title(array(
			// 'text'=> '<i class="icon-"></i>',
			'class'=>"btn-txt btn-icon fcw",
			'icon' => 'ellipsis-v'
		))
		->position( 'right' )

		->option('แก้ไข')
			->link( URL."agent/member/edit/{$this->item['m_id']}")
			->attr('data-plugins', 'dialog')

		->option('เปลี่ยนรหัสผ่าน')
			->link( URL."agent/member/change_password/{$this->item['m_id']}")
			->attr('data-plugins', 'dialog');

		if( $this->item['status']=='play' ){

			$actions->option('หยุดการใช้งาน')
			->link( URL."agent/member/change_status/{$this->item['m_id']}/pause")
			->attr('data-plugins', 'dialog');
		}
		else if( $this->item['status']=='pause' ) {

			$actions->option('เปิดการใช้งาน')
			->link( URL."agent/member/change_status/{$this->item['m_id']}/play")
			->attr('data-plugins', 'dialog');
		}

	$actions
		->divider()

		->option('ลบ')
			->link( URL."agent/member/del/{$this->item['m_id']}")
			->attr('data-plugins', 'dialog');

	echo $actions->getPluginJquey();

?></div>