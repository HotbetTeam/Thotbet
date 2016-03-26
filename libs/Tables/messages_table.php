<?php
	
class Messages_Table extends Model {
	
	public function __construct() {
		parent::__construct();
	}

	public function lists($options=array()){

        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "c.updated<=:time";
        $where = array(
            ':time' => $date
        );

        $sth = $this->db->prepare("SELECT COUNT(*) 
            FROM conversation c INNER JOIN users u ON c.user_id=u.user_id
            WHERE {$where_str}");
        $sth->execute( $where );

        $arr['total'] = intval($sth->fetchColumn());

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT 
        	c_id as id, c.updated, u.user_id, name 
            FROM conversation c INNER JOIN users u ON c.user_id=u.user_id
            WHERE {$where_str}
            ORDER BY c.updated DESC {$limit}",
            $where        
        ) );

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
        $sth = $this->db->prepare("SELECT * 
            FROM conversation c INNER JOIN users u ON c.user_id=u.user_id
            WHERE c.c_id=:id 
            LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        $result = $sth->fetch( PDO::FETCH_ASSOC );
        if( !empty($result) ){
            $result = $this->convert($result);
        }
        return $result;
    }

    public function convert($data) {
        
    	// latest
    	$sth = $this->db->prepare("SELECT 
    		c.text, c.user_id, u.name 
    		FROM conversation_reply c 
    			LEFT JOIN users u ON c.user_id=u.user_id
    		WHERE c_id_fk=:id ORDER BY time DESC LIMIT 1");
        $sth->execute( array(
            ':id' => $data['id']
        ) );
        $data['latest'] = $sth->fetch( PDO::FETCH_ASSOC );
        

        $data['url'] = URL.'inbox/'.$data['id'];
        
        return $data;
    }

    /**/
    /**/
    public function getMessage( $options=array() ) {
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),

            'id' => isset($_REQUEST['id'])? $_REQUEST['id']:null,

            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);
        
        $where_str = "c_id_fk=:id AND time<=TIMESTAMP(:d)";
        $where = array( ':id'=>$options['id'], ':d' => $date );

   		$arr['total'] = $this->db->count('conversation_reply', $where_str, $where );

   		$limit = $this->limited( $options['limit'], $options['pager'] );
   		$arr['lists'] = $this->db->select("SELECT 
            c.c_id_fk, c.cr_id, c.text, c.time
            , u.user_id, u.name
            FROM conversation_reply c LEFT JOIN users u ON c.user_id=u.user_id
            WHERE {$where_str} ORDER BY time DESC {$limit}", $where);

   		if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

   		$arr['options'] = $options;

   		return $arr;
    }

    public function getUserAdmin() {
        return $this->query('users')->buildFrag( $this->db->select("SELECT user_id,name,email,phone_number,u_conversation_key_id as conversation_key_id FROM users WHERE access_id=1") );
    }
}