<?php

$tr = "";
if( !empty($this->results['lists']) ){

    foreach ($this->results['lists'] as $i => $item) { 

        // $this->item = $item;
        $cls = $i%2 ? 'even' : "odd";

        $timestamp = strtotime($item['op_date']);
		$theTime = date("H.s", $timestamp);
        $date_str = '<div>'.$this->fn->q('time')->normal( $timestamp ).'</div><span class="fcg">'.$theTime.'น.</span>';


        $tr .= '<tr class="'.$cls.'" data-id="'.$item['op_id'].'">'.
            '<td class="date">'.$date_str.'</td>'.
            
            '<td class="status_name">'.$item['name'].'</td>'.
            '<td class="status_name">'.$item['op_customer'].'</td>'.
            // '<td class="name"></td>'.
            '<td class="name"><div class="group-btn">
                <a data-plugins="dialog" href="'.URL.'operator/form/'.$item['op_id'].'" class="btn"><i class="icon-pencil mrs"></i>แก้ไข</a>
                <a data-plugins="dialog" href="'.URL.'operator/del/'.$item['op_id'].'" class="btn"><i class="icon-trash mrs"></i>ลบ</a>
            </div></td>'.

        '</tr>';

    }
}

$table = '<table><tbody>'. $tr. '</tbody></table>';