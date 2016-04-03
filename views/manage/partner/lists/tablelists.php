<?php

$tr = "";
$tr_total = "";
if( !empty($this->results['lists']) ){ 

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) { 

        $timestamp = strtotime($item['partner_created']);
        $theTime = date("H.s", $timestamp);
        $item['partner_created_str'] = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div>'.'<span class="fsm fcg">'.$theTime.'น.</span>';


        $timestamp = strtotime($item['partner_updated']);
        $theTime = date("H.s", $timestamp);
        $item['partner_updated_str'] = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div>'.'<span class="fsm fcg">'.$theTime.'น.</span>';

        // $this->item = $item;
        $cls = $i%2 ? 'even' : "odd";

        // set Name
        $name = '<a class="fwb" href="'.$item['url'].'">'.$item['partner_name'].'</a>
				<div class="fsm mts">'.

                    ( !empty( $item['partner_email'] )
                    ? '<span class="mrs"><i class="icon-envelope mrs"></i>'. $item['partner_email'].'</span>'
                    : ''  ).

                    ( !empty( $item['partner_tel'] )
					? '<span class="mrs"><i class="icon-phone mrs"></i>'. $item['partner_tel'].'</span>'
                    : ''  ).

				'</div>';


        $actions = $this->ui->toggle()
            ->title(array(
                'text'=> '<i class="icon-cog"></i>',
                'class'=>"btn-txt btn",
            ))
            ->position( 'right' )

            ->option('เปลี่ยนรหัสผ่าน')
                ->link(URL."partner/change_pass/{$item['partner_id']}")
                ->attr('data-plugins', 'dialog');

/*            if( $item['stu_display']=='enabled' ){

                $actions->option('ปิดการใช้งาน')
                ->link( URL."partner/change_display/{$item['partner_id']}/disabled")
                ->attr('data-plugins', 'dialog');
            }
            else{
                $actions->option('เปิดการใช้งาน')
                ->link( URL."partner/change_display/{$item['partner_id']}/enabled")
                ->attr('data-plugins', 'dialog');
            }*/

            $actions
                ->divider()

                ->option('ลบ')
                    ->link( URL."partner/del/{$item['partner_id']}")
                    ->attr('data-plugins', 'dialog');

       
        $tr .= '<tr class="'.$cls.'" data-id="'.$item['partner_id'].'">'.

            // '<td class="check-box"><label class="checkbox"><input id="toggle_checkbox" type="checkbox" value="'.$item['partner_id'].'"></label></td>'.
            // '<td class="star"></td>'.
            '<td class="date">'.$item['partner_created_str'].'</td>'.

            '<td class="name">'.$name.'</td>'.


            '<td class="status">'.$item['partner_total_member'].'</td>'.

            '<td class="date">'.$item['partner_updated_str'].'</td>'.

            '<td class="actions">'.
                '<span class="group-btn" style="width:150px">'.
                    '<a href="'.URL.'partner/edit/'.$item['partner_id'].'" data-plugins="dialog" class="btn"><i class="icon-pencil"></i><span class="mls btn-text">แก้ไข</span></a>'.
                    $actions->getPluginJquey().
                '<span>'.
            '</td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';