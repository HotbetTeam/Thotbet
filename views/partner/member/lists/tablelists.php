<?php

$tr = "";
if( !empty($this->results['lists']) ){

    foreach ($this->results['lists'] as $i => $item) { 

        // $this->item = $item;
        $cls = $i%2 ? 'even' : "odd";

        $timestamp = strtotime($item['created']);
		$theTime = date("H.s", $timestamp);
        $date_str = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div><span class="fcg">'.$theTime.'น.</span>';


        $name = '<a href="'.URL.'partner/member/'.$item['m_id'].'" class="fwb">'.$item['name'].'</a>
				<div class="mts fsm">'. 
					( !empty($item['email']) 
						? '<span class="mrs"><i class="icon-envelope mrs"></i>'. $item['email'].'</span>'
						: '' ).
					( !empty($item['phone_number']) 
						? '<span class="mrs"><i class="icon-phone mrs"></i>'. $item['phone_number'].'</span>'
						: '') .
				'</div>';

		$actions = $this->ui->toggle()
			->title(array(
				'text'=> '<i class="icon-cog"></i>',
				'class'=>"btn-txt btn",
			))
			->position( 'right' )

			->option('เปลี่ยนรหัสผ่าน')
				->link(URL."partner/member/change_password/{$item['m_id']}")
				->attr('data-plugins', 'dialog');

			if( $item['status']=='play' ){

				$actions->option('หยุดการใช้งาน')
				->link( URL."member/change_status/{$item['m_id']}/pause")
				->attr('data-plugins', 'dialog');
			}
			else if( $item['status']=='pause' ) {

				$actions->option('เปิดการใช้งาน')
				->link( URL."member/change_status/{$item['m_id']}/play")
				->attr('data-plugins', 'dialog');
			}

			$actions
				->divider()

				->option('ลบ')
					->link( URL."partner/member/del/{$item['m_id']}")
					->attr('data-plugins', 'dialog');


        $tr .= '<tr class="'.$cls.'" data-id="'.$item['m_id'].'">'.
            '<td class="date">'.$date_str.'</td>'.

            
            '<td class="status"><i class="icon-'.$item['status'].'"></i></td>'.
            

            '<td class="username">'.$item['game_user'].'</td>'.
            	
            '<td class="name">'.$name.'</td>'.

         
            '<td class="status">'.$item['level_name'].'</td>'.
            

            '<td class="number">'.number_format( $item['point_show']).'</td>'.
            
            
            '<td class="actions">'.

            	'<span class="group-btn" style="width:130px;">'.	
            			'<a class="btn" href="'.URL.'partner/member/edit/'.$item['m_id'].'" data-plugins="dialog"><i class="icon-pencil mrs"></i>แก้ไข</a>'.
            			$actions->getPluginJquey().
            		  '</span>'.
            '</td>'.

        '</tr>';

    }
}

$table = '<table><tbody>'. $tr. '</tbody></table>';