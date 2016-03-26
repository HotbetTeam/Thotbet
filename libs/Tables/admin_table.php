<?php
    
class Admin_Table extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function lists($options=array()){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'desc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'u.updated',

            'access_id' => isset($_REQUEST['access_id'])? $_REQUEST['access_id']:null,
            // 'display' => isset($_REQUEST['display'])? $_REQUEST['display']:'enabled',
            
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $width_str = "WHERE u.created<=:time";
        $width = array( ':time' => $date );

        $width_str .= !empty($options['access_id'])
            ? " AND u.access_id='{$options['access_id']}'"
            : " AND u.access_id!=2";

        $sql = "users u INNER JOIN access a ON u.access_id=a.access_id";

        // echo "SELECT COUNT(*) FROM {$sql} {$width_str}"; die;

        $sth = $this->db->prepare("SELECT COUNT(*) FROM {$sql} {$width_str}");
        $sth->execute( $width );
        $arr['total'] = $sth->fetchColumn();


        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT 
            u.user_id, u.name, u.email, u.phone_number, a.access_name, u.created, u.updated
            FROM {$sql} {$width_str}
            {$orderby} {$limit}", $width ) );

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

    public function get($id){
        
        $sth = $this->db->prepare("SELECT  
            m.m_id, m.user_id, m.user, u.name, u.email, u.phone_number, a.access_name, m.created
            FROM member m LEFT JOIN (users u INNER JOIN access a ON u.access_id=a.access_id) ON m.user_id=u.user_id
            WHERE m.user_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) );
        } return array();
    }

    public function convert($result){

        $result['url'] = URL."admin/{$result['user_id']}";
        return $result;
    }

    public function insert(&$data)
    {

        $data['created'] = date('c');
        $data['updated'] = date('c');
        
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

}