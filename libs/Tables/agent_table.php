<?php
	
class Agent_Table extends Model {
	
	public function __construct() {
		parent::__construct();
	}

    /**/
    /* config Data */
    public $_field = " * ";
    public $_table = "agent";
    public function options($options=array()) {
        return array_merge( array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'desc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'agent_created',
            
            'status' => isset($_REQUEST['status'])? $_REQUEST['status']:null,

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options );
    }

    /* manage */
    public function insert(&$data){

        $data['agent_created'] = date('c');
        $data['agent_updated'] = date('c');
        
        $this->db->insert('agent', $data);
        $data['agent_id'] = $this->db->lastInsertId();
    }
    public function update($id, $data) {
        $data['agent_updated'] = date('c');
        $this->db->update('agent', $data, "`agent_id`={$id}");
    }
    public function delete($id) {
        $this->db->delete('agent', "`agent_id`={$id}");
    }

    // ดึงข้อมูลครั้งละ หลายๆ แถว
    public function lists($options=array()){
        $options = $this->options( $options );
        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "agent_created<=:time";
        $where_arr = array( ':time' => $date );

        $arr['total'] = $this->db->count( $this->_table, $where_str, $where_arr );

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        return $arr;
    }
    // ดึงข้อมูลครั้งละ 1 แถว
    public function get($id){
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE agent_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) );
        } return array();
    }

    // แปลงข้อมูล
    public function buildFrag($results){
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert($value);
        }

        return $data;
    }
    public function convert($result){
        // $result = array_merge($result, $this->getMember($result['user_id']));
        
        $result['image_url'] = IMAGES.'avatar/error/admin.png';
        return $result;
    }

    /* join */
    /* member */
    public function member( $aid, $options=array() ) {
        $options = $this->query('member')->options($options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "m_created<=:time";
        $where_arr = array( ':time' => $date );

        $where_str .= empty($options['status']) || $options['status']=='all'
            ? " AND (m.status!='verify' AND m.status!='cancel')"
            : " AND m.status='{$options['status']}'";

        $arr['total'] = $this->count( $this->query('member')->_table, $where_str, $where_arr );

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->query('member')->buildFrag( $this->db->select("SELECT {$this->query('member')->_field} FROM {$this->query('member')->_table} {$where_str} {$orderby} {$limit}", $where_arr ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;
        return $arr;
    }
    public function getMember( $mid, $aid ){
        
        $sth = $this->db->prepare("SELECT {$this->query('member')->_field()} FROM {$this->query('member')->_table()} WHERE m_agent_id=:aid AND m_id=:mid LIMIT 1");
        $sth->execute( array(
            ':aid' => $aid,
            ':mid' => $mid
        ) );

        if( $sth->rowCount()==1 ){
            return $this->query('member')->convert( $sth->fetch( PDO::FETCH_ASSOC ) );
        } return array();
    }


    /* ตรวจสอบข้อมูล */

    /* login */
    public function login($user, $pess){

        $sth = $this->db->prepare("SELECT agent_id as id FROM agent WHERE (agent_email=:login AND agent_password=:pass) OR (agent_tel=:login AND agent_password=:pass)");

        $sth->execute( array(
            ':login' => $user,
            ':pass' => $pess
        ) );

        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            return $fdata['id'];
        } return false;
    }
    /* ข้อมูลซ่ำ  */
    public function duplicate($text){
        return $this->db->count("agent", "agent_email='{$text}' OR agent_tel='{$text}'");
    }

}