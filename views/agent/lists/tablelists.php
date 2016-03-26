<?php

$tr = "";
$tr_total = "";
if( !empty($this->results['lists']) ){ 

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) { 

        $timestamp = strtotime($item['agent_created']);
        $theTime = date("H.s", $timestamp);
        $item['agent_created_str'] = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div>'.'<span class="fsm fcg">'.$theTime.'น.</span>';


        $timestamp = strtotime($item['agent_updated']);
        $theTime = date("H.s", $timestamp);
        $item['agent_updated_str'] = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div>'.'<span class="fsm fcg">'.$theTime.'น.</span>';

        // $this->item = $item;
        $cls = $i%2 ? 'even' : "odd";

        // set Name
        $name = '<a class="fwb" href="'.$item['url'].'">'.$item['agent_name'].'</a>
				<div class="fsm mts">'.

                    ( !empty( $item['agent_email'] )
                    ? '<span class="mrs"><i class="icon-envelope mrs"></i>'. $item['agent_email'].'</span>'
                    : ''  ).

                    ( !empty( $item['agent_tel'] )
					? '<span class="mrs"><i class="icon-phone mrs"></i>'. $item['agent_tel'].'</span>'
                    : ''  ).

				'</div>';


        $actions = $this->ui->toggle()
            ->title(array(
                'text'=> '<i class="icon-cog"></i>',
                'class'=>"btn-txt btn",
            ))
            ->position( 'right' )

            ->option('เปลี่ยนรหัสผ่าน')
                ->link(URL."agent/change_pass/{$item['agent_id']}")
                ->attr('data-plugins', 'dialog');

/*            if( $item['stu_display']=='enabled' ){

                $actions->option('ปิดการใช้งาน')
                ->link( URL."agent/change_display/{$item['agent_id']}/disabled")
                ->attr('data-plugins', 'dialog');
            }
            else{
                $actions->option('เปิดการใช้งาน')
                ->link( URL."agent/change_display/{$item['agent_id']}/enabled")
                ->attr('data-plugins', 'dialog');
            }*/

            $actions
                ->divider()

                ->option('ลบ')
                    ->link( URL."agent/del/{$item['agent_id']}")
                    ->attr('data-plugins', 'dialog');

       
        $tr .= '<tr class="'.$cls.'" data-id="'.$item['agent_id'].'">'.

            // '<td class="check-box"><label class="checkbox"><input id="toggle_checkbox" type="checkbox" value="'.$item['agent_id'].'"></label></td>'.
            // '<td class="star"></td>'.
            '<td class="date">'.$item['agent_created_str'].'</td>'.

            '<td class="name">'.$name.'</td>'.


            '<td class="status">'.$item['agent_total_member'].'</td>'.

            '<td class="date">'.$item['agent_updated_str'].'</td>'.

            '<td class="actions">'.
                '<span class="group-btn" style="width:150px">'.
                    '<a href="'.URL.'agent/edit/'.$item['agent_id'].'" data-plugins="dialog" class="btn"><i class="icon-pencil"></i><span class="mls btn-text">แก้ไข</span></a>'.
                    $actions->getPluginJquey().
                '<span>'.
            '</td>'.

        '</tr>';

        //set $total

        /*if( !isset($total['number']) ) $total['number'] = 0;
        $total['number'] += $seq;

        if( !isset($total['total_qty']) ) $total['total_qty'] = 0;
        $total['total_qty'] += $item['total_qty'];

        if( !isset($total['total_amount']) ) $total['total_amount'] = 0;
        $total['total_amount'] += $item['total_amount'];*/
        
    }

    // result amount
    /*$tr_total = '<tfoot><tr class="amount">'.
    	'<td colspan="3" class="tar">รวมทั้งหมด</td>'.
    	'<td class="number">'.$total['total_qty'].'</td>'.
        '<td class="number">'.number_format($total['total_amount'],2).'</td>'.
    	'<td class="actions"></td>'.
    '</tr></tfoot>';*/
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';