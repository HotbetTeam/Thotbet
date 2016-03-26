<?php
    
class Member_Table extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    private $field = "   m.user_id, m.user, m.created, m.sum_point, m.poit_lok, m.status 
                    , m.m_id, m.level_id, m_level_name
                    , u.name, u.email, u.phone_number, a.access_name";

    private $sql = "member m INNER JOIN member_level l ON m.level_id=l.m_level_id
            LEFT JOIN 
                (users u INNER JOIN access a ON u.access_id=a.access_id) 
            ON m.user_id=u.user_id ";

            
    public function lists($options=array()){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'desc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'m.created',
            
            'status' => isset($_REQUEST['status'])? $_REQUEST['status']:null,

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        // 
        $width_str = "WHERE m.created<=:time";
        $width_arr = array( ':time' => $date );


        $width_str .= empty($options['status']) || $options['status']=='all'
            ? " AND (m.status!='verify' AND m.status!='cancel')"
            : " AND m.status='{$options['status']}'";


        $sth = $this->db->prepare("SELECT COUNT(*) FROM {$this->sql} {$width_str}");
        $sth->execute( $width_arr );
        $arr['total'] = $sth->fetchColumn();

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->field} FROM {$this->sql} {$width_str} {$orderby} {$limit}", $width_arr ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        // print_r($arr); die;

        return $arr;
    }
    public function buildFrag($results){
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert($value);
        }

        return $data;
    }

    public function get($id, $field='m.user_id'){

        
        $sth = $this->db->prepare("SELECT {$this->field} FROM {$this->sql} WHERE {$field}=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) );
        } return array();
    }

    public function convert($result){

        $result['url'] = URL."member/{$result['user_id']}";
        return $result;
    }

    public function is_user($text)
    {
        return $this->db->count("member", "user='{$text}'");
    }

    public function insert(&$data)
    {

        $data['created'] = date('c');
        $data['updated'] = date('c');

        if( empty($data['level_id']) ){
            $data['level_id'] = 1;
        }
        
        $this->db->insert('member', $data);
        $data['m_id'] = $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $data['updated'] = date('c');
        $this->db->update('member', $data, "`m_id`={$id}");
    }

    public function delete($id)
    {
        $this->db->delete('member', "`m_id`={$id}");
    }

    /**/
    /* status */
    public function statusCounts(){
        return array(
            /*'all' => array(
                'text' => 'สมาชิกทั้งหมด',
                'total' => $this->getCountStatus( 'all' )
            ),*/
            'play' => array(
                'text' => 'เปิดการใช้งาน',
                'total' => $this->getCountStatus( 'play' )
            ),
            'pause' => array(
                'text' => 'หยุดการใช้งาน',
                'total' => $this->getCountStatus( 'pause' )
            ),
            'verify' => array(
                'text' => 'รอการอนุมัติ',
                'total' => $this->getCountStatus( 'verify' )
            ),

        );
    }
    public function getCountStatus($status){
        return $this->db->count('member', "status=:t", array(':t'=>$status));
    }

    /**/
    /* playing */
    public function playing($id, $options = array()) {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:null,
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'pl_date',

            'start_date' => isset($_REQUEST['start_date'])? $_REQUEST['start_date']: null,
            'end_date' => isset($_REQUEST['end_date'])? $_REQUEST['end_date']: null,

            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
            'more' => true
                ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        // get field
        $field = "    u.user_id, m.user, u.name
                    , m_id 
                    , pl_id
                    , pl_menber_point AS pl_menber_point_cal
                    , pl_actual_point AS pl_actual_point_cal
                    , pl_wagers
                    , pl_bet_amount
                    , pl_menber 
                    , pl_actual, pl_date";
                    

        $sql = "playing AS p LEFT JOIN 
            ( member AS m INNER JOIN users AS u ON m.user_id=u.user_id  ) 
        ON pl_m_id=m_id ";

        $where_str = "";
        $where_arr = array();

        if( !empty($options['start_date']) && !empty($options['end_date']) ){

            $where_arr[':start_date'] = $options['start_date'];
            $where_arr[':end_date'] = $options['end_date'];

            $where_str .= !empty($where_str) ? " AND " : "";
            $where_str .= "(pl_date BETWEEN :start_date AND :end_date)";
        }

        $where_arr[':mid'] = $id;
        $where_str .= !empty($where_str) ? " AND " : "";
        $where_str .= "pl_m_id=:mid";

        // total      
        $arr['total'] = $this->db->count( $sql, $where_str,  $where_arr);

        // lists
        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] = $this->query('playing')->buildFrag( $this->db->select("SELECT {$field} FROM {$sql} WHERE {$where_str} {$orderby} {$limit}", $where_arr) );

        // print_r($where_arr); die;
        // echo "SELECT {$field} {$sql} WHERE {$where_str} {$orderby} {$limit}"; die;

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        // print_r($arr); die;
        return $arr;
        
    }


    /**/
    /* Point */
    public function setPoint($data) {
        
        foreach ($data as $key => $value) {
            $this->db->update("member_point", array(
                "point_cal"=>$value,
                'point_updated' => date('c')
            ), "point_key='{$key}'" );
        }
        
    }
    public function getPoint() {
        

        $fdate = $this->db->select("SELECT * FROM member_point");

        $data = array();
        foreach ($fdate as $key => $value) {
            $data[ $value['point_key'] ] = $value['point_cal'];
        }

        return $data;
    }


    /**/
    /* Level */
    public function levelLists( $options=array() ) {
        $options = array_merge(array(
            
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'asc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'m_level_score',
            
        ), $options);

        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] =  $this->levelBuildFrag( $this->db->select("SELECT * FROM member_level {$orderby}") );

        $arr['options'] = $options;

        return $arr;
    }
    public function levelBuildFrag($results){
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->levelConvert($value);
        }

        return $data;
    }
    public function levelConvert($result) {
        $result['membership'] = $this->db->count('member', "level_id=:id", array(':id'=>$result['m_level_id']));

        return $result;
    }

    public function setLevel(&$data){
        
        $data['m_level_updated'] = date('c');
        $this->db->insert('member_level', $data);
        $data['m_level_id'] = $this->db->lastInsertId();
    }
    public function getLevel($id) {

        $sth = $this->db->prepare("SELECT * FROM member_level WHERE m_level_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );
        
        return $this->levelConvert( $sth->fetch( PDO::FETCH_ASSOC ) );
        
    }
    public function editLevel($id, $data) {
        $this->db->update('member_level', $data, "`m_level_id`={$id}");
    }

    public function delLevel($id) {
        $this->db->delete('member_level', "`m_level_id`={$id}");
    }

    public function upLevel($id, $poit, $level=1) {
        
        $data = $this->levelLists();
        foreach ($data['lists'] as $key => $value) {
            
            if( $poit >= $value['m_level_score'] && $value['m_level_auto'] ){
                $level = $value['m_level_id'];
            }
        }

        $this->update($id, array('level_id'=>$level));
    }
}