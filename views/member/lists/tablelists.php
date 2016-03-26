<?php

$tr = "";
if( !empty($this->results['lists']) ){

    foreach ($this->results['lists'] as $i => $item) { 

        // $this->item = $item;
        $cls = $i%2 ? 'even' : "odd";

        $timestamp = strtotime($item['created']);
		$theTime = date("H.s", $timestamp);
        $date_str = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div><span class="fcg">'.$theTime.'น.</span>';


        $name = '<a class="fwb"'.( $this->status!='verify' ? ' href="'.$item['url'].'"':'' ).'>'.$item['name'].'</a>
				<div class="mts fsm">'. 
					( !empty($item['email']) 
						? '<span class="mrs"><i class="icon-envelope mrs"></i>'. $item['email'].'</span>'
						: '' ).
					( !empty($item['phone_number']) 
						? '<span class="mrs"><i class="icon-phone mrs"></i>'. $item['phone_number'].'</span>'
						: '') .
				'</div>';

		$agent = empty($item['agent_id'])
			? '-'
			: '<a href="'.URL.'manage/agent/'.$item['agent_id'].'">'.$item['agent']['agent_name'].'</a>';

		$actions = $this->ui->toggle()
			->title(array(
				'text'=> '<i class="icon-cog"></i>',
				'class'=>"btn-txt btn",
			))
			->position( 'right' )

			->option('เปลี่ยนรหัสผ่าน')
				->link(URL."member/change_pass/{$item['m_id']}")
				->attr('data-plugins', 'dialog')

			->option('เปลี่ยนระดับสมาชิก')
				->link(URL."member/change_level/{$item['m_id']}")
				->attr('data-plugins', 'dialog')

				/*->divider()->option('ส่งข้อความ')
				->link(URL."users/form/{$item['m_id']}")
				->attr('data-plugins', 'dialog')*/

			->divider();


			$actions->option('ข้อมูลการเล่นเกมส์')
				->link( URL."member/change_about_game/{$item['m_id']}")
				->attr('data-plugins', 'dialog')

			->divider();

			if( $item['status']=='play' ){

				$actions->option('หยุดการใช้งาน')
				->link( URL."member/change_status/{$item['m_id']}/pause")
				->attr('data-plugins', 'dialog');
			}
			else{

				$actions->option('เปิดการใช้งาน')
				->link( URL."member/change_status/{$item['m_id']}/play")
				->attr('data-plugins', 'dialog');
			}

			$actions
				->divider()

				->option('ลบ')
					->link( URL."member/del/{$item['m_id']}")
					->attr('data-plugins', 'dialog');


        $tr .= '<tr class="'.$cls.'" data-id="'.$item['m_id'].'">'.
            '<td class="date">'.$date_str.'</td>'.

            ( $this->status!='verify' 
            	? '<td class="status"><i class="icon-'.$item['status'].'"></i></td>'
            	: ''
            ).

            ( $this->status!='verify' 
            	? '<td class="username">'.$item['game_user'].'</td>'
            	: ''
            ).
            '<td class="name">'.$name.'</td>'.

            ( $this->status!='verify' 
            	? '<td class="status">'.$item['level_name'].'</td>'
            	: ''
            ).

            ( $this->status!='verify' 
            	?   '<td class="number">'.number_format( $item['point'] ).'</td>'. 
            		'<td class="number">'.number_format( $item['point_show']).'</td>'
            	: ''
            ).

            '<td>'.$agent.'</td>'.
            
            '<td class="actions">'.


            	( $this->status=='verify'
            		? '<a class="btn btn-blue" href="'.URL.'member/confrim/'.$item['m_id'].'" data-plugins="dialog" >อนุมัติ</a>'.
            			'<a class="btn" href="'.URL.'member/del/'.$item['m_id'].'" data-plugins="dialog">ลบ</a>'
            			
            		: '<span class="group-btn" style="width:130px;">'.	
            			'<a class="btn" href="'.URL.'member/edit/'.$item['m_id'].'" data-plugins="dialog"><i class="icon-pencil mrs"></i>แก้ไข</a>'.
            			$actions->getPluginJquey().
            		  '</span>'
            	).
            '</td>'.

        '</tr>';

    }
}

$table = '<table><tbody>'. $tr. '</tbody></table>';