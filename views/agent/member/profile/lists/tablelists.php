<?php

$tr = "";
$tr_total = "";
if( !empty($this->results['lists']) ){

    foreach ($this->results['lists'] as $i => $item) { 

        if( isset($item['_total']) ){


            $tr .= '<tr class="total">'.

            '</tr>';
            continue;
        }

        // $this->item = $item;
        $cls = $i%2 ? 'even' : "odd";

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['pl_id'].'">'.
           '<td class="date">'.$this->fn->q('time')->normal( strtotime($item['pl_date']) ).'</td>'.
            // '<td class="username">'.$item['user'].'</td>'.
            // '<td class="name"><a href="'.$item['url'].'">'.$item['name'].'</a></td>'.
            '<td class="number">'.$item['pl_wagers_str'].'</td>'.    
            '<td class="number">'.$item['pl_bet_amount_str'].'</td>'.
            '<td class="number">'.$item['pl_menber_str'].'</td>'.
            '<td class="number point">'.$item['pl_menber_point_str'].' <span>('.$item['pl_menber_point_cal_percent'].'%)</span></td>'.
            '<td class="number">'.$item['pl_actual_str'].'</td>'.
            '<td class="number point">'.$item['pl_actual_point_str'].'<span>('.$item['pl_actual_point_cal_percent'].'%)</span></td>'.
            '<td class="number">'.$item['pl_sum_point_str'].'</td>'.
            // '<td class="actions"><a href="'.URL.'playing/form/'.$item['pl_id'].'" data-plugins="dialog" class="btn"><i class="icon-pencil"></i></a></th>'.

        '</tr>';


        if( !isset($total['pl_wagers']) ) $total['pl_wagers'] = 0;
        $total['pl_wagers'] +=$item['pl_wagers'];

        if( !isset($total['pl_bet_amount']) ) $total['pl_bet_amount'] = 0;
        $total['pl_bet_amount'] += $item['pl_bet_amount'];

        if( !isset($total['pl_menber']) ) $total['pl_menber'] = 0;
        $total['pl_menber'] += $item['pl_menber'];

        if( !isset($total['pl_menber_point']) ) $total['pl_menber_point'] = 0;
        $total['pl_menber_point'] += $item['pl_menber_point'];

        if( !isset($total['pl_actual']) ) $total['pl_actual'] = 0;
        $total['pl_actual'] += $item['pl_actual'];

        if( !isset($total['pl_actual_point']) ) $total['pl_actual_point'] = 0;
        $total['pl_actual_point'] += $item['pl_actual_point'];

        if( !isset($total['pl_sum_point']) ) $total['pl_sum_point'] = 0;
        $total['pl_sum_point'] += $item['pl_sum_point'];

    }

    // result amount
    $tr_total = '<tfoot><tr class="amount">'.
        '<td class="date text">รวมทั้งหมด</td>'.
        '<td class="number">'.$total['pl_wagers'].'</td>'.
        '<td class="number">'.number_format($total['pl_bet_amount'],2).'</td>'.
        '<td class="number">'.number_format($total['pl_menber'],2).'</td>'.
        '<td class="number">'.number_format($total['pl_menber_point'],2).'</td>'.
        '<td class="number">'.number_format($total['pl_actual'],2).'</td>'.
        '<td class="number">'.number_format( round($total['pl_actual_point'],2,PHP_ROUND_HALF_DOWN),2 ).'</td>'.
        '<td class="number">'.number_format( round($total['pl_sum_point'],2,PHP_ROUND_HALF_DOWN),2 ).'</td>'.
        // '<td colspan="actions"></td>'.
    '</tr></tfoot>';
}

$table = "<table><tbody>{$tr}</tbody>{$tr_total}</table>";