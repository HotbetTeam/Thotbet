<?php

class Playing_table extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function lists($options = array()) {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:null,
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'p.pl_date',

            'start_date' => isset($_REQUEST['start_date'])? $_REQUEST['start_date']: null,
            'end_date' => isset($_REQUEST['end_date'])? $_REQUEST['end_date']: null,

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
            'more' => true
                ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        // get field
        $field = "    m_username as username, m_name as name, m_id 
                    , m.game_user, m.game_pass
                    , l.lev_name as level_name
                    , pl_id
                    , pl_menber_point AS pl_menber_point_cal
                    , pl_actual_point AS pl_actual_point_cal
                    , SUM(pl_wagers) AS pl_wagers
                    , SUM(pl_bet_amount) AS pl_bet_amount
                    , SUM(pl_menber) AS pl_menber 
                    , SUM(pl_actual) AS pl_actual
                    , pl_date";

        $sql = "FROM playing AS p LEFT JOIN 
            ( member AS m INNER JOIN member_level AS l ON m_level_id=l.lev_id  ) 
        ON pl_m_id=m_id ";

        //  

        $where_str = "";
        $where_arr = array();

        if( !empty($options['start_date']) && !empty($options['end_date']) ){

            $where_arr[':start_date'] = $options['start_date'];
            $where_arr[':end_date'] = $options['end_date'];

            $where_str .= !empty($where_str) ? " AND " : "WHERE ";
            $where_str .= "(p.pl_date BETWEEN :start_date AND :end_date)";
        }

        // total
        $sqlx ="SELECT p.pl_m_id {$sql} {$where_str} GROUP BY p.pl_m_id";         
            $count = $this->db->select($sqlx, $where_arr);
            $arr['total'] = count($count);
 

        // lists
        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$field} {$sql} {$where_str} GROUP BY p.pl_m_id {$orderby} {$limit}", $where_arr) );

        /*$options['sql'] = "SELECT
            pl_id, pl_wagers, pl_bet_amount, pl_menber, pl_actual
            {$sql} {$where_str} {$orderby} {$limit}";*/

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        // sprintf('%0.2f', $unpadded);

        // print_r($arr); die;
        return $arr;
    }

    public function buildFrag($results) {
        
        $data = array();
        foreach ($results as $key => $value) {
            if (empty($value)) continue;
            $data[] = $this->convert($value);
        }
        
        return $data;
    }

    public function get($id) {

        // get field
        $field = "    m_username as username, m_name as name, m_id 
                    , l.lev_name as level_name
                    , pl_id
                    , pl_menber_point AS pl_menber_point_cal
                    , pl_actual_point AS pl_actual_point_cal
                    , pl_wagers
                    , pl_bet_amount
                    , pl_menber 
                    , pl_actual
                    , pl_date";


        $sql = "FROM playing AS p LEFT JOIN 
            ( member AS m INNER JOIN member_level AS l ON m_level_id=l.lev_id  ) 
        ON pl_m_id=m_id ";

        $sth = $this->db->prepare("SELECT {$field} {$sql} WHERE pl_id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id
        ));

        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $result = $this->convert($result);
        }
        
        return $result;
    }

    public function convert($data) {

        // /(value)
        // Membe
        $data['pl_wagers_str'] = number_format($data['pl_wagers']);
        $data['pl_bet_amount_str'] = number_format($data['pl_bet_amount'], 2);
        $data['pl_menber_str'] = number_format($data['pl_menber'], 2);
        $data['pl_menber_point_cal'] = round($data['pl_menber_point_cal'], 6, PHP_ROUND_HALF_DOWN);
        $data['pl_menber_point_cal_percent'] = $data['pl_menber_point_cal']*100;
        $data['pl_menber_point'] = $data['pl_menber'] * ($data['pl_menber_point_cal']*-1);
        $data['pl_menber_point_str'] = number_format($data['pl_menber_point'], 2);

        $data['pl_actual_str'] = number_format($data['pl_actual'], 2);
        $data['pl_actual_point_cal'] = round($data['pl_actual_point_cal'], 6, PHP_ROUND_HALF_DOWN);
        $data['pl_actual_point_cal_percent'] = $data['pl_actual_point_cal']*100;
        $data['pl_actual_point'] = $data['pl_actual'] * $data['pl_actual_point_cal'];
        $data['pl_actual_point_str'] = number_format($data['pl_actual_point'], 2);

        $data['pl_sum_point'] = $data['pl_menber_point'] + $data['pl_actual_point'];
        $data['pl_sum_point_str'] = number_format($data['pl_sum_point'], 2);

        $data['url'] = URL . 'member/' . $data['m_id'];
        return $data;
    }

    public function insert(&$data) {
        $this->db->insert('playing', $data);
        $data['pl_id'] = $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $this->db->update('playing', $data, "`pl_id`={$id}");
    }
}