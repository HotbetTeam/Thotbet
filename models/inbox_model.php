<?php

class Inbox_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

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
            c.c_id_fk, c.cr_id, c.text, c.time, c_key_id
            FROM conversation_reply c
            WHERE {$where_str} ORDER BY time DESC {$limit}", $where);


        if( ($options['pager']*$options['limit']) >= $arr['total'] )  $options['more'] = false;
        $arr['options'] = $options;

        $arr['key_id'] = $this->me['conversation_key_id']==0 
            ? $this->getKey( $this->me['user_id'] )
            : $this->me['conversation_key_id'];

        return $arr;
    }

    public function getKey($id, $type='user'){
        $sth = $this->db->prepare("SELECT * FROM conversation_key WHERE obj_id=:id AND obj_type=:type LIMIT 1");
        $sth->execute( array(
            ':id' => $id,
            ':type' => $type
        ) );

        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            $key = $fdata['conversation_key_id'];
        }
        else{
            $this->db->insert('conversation_key', array('obj_type'=>$type, 'obj_id'=>$id));
            $key = $this->db->lastInsertId();
            $this->query( $type )->update( $id, array('u_conversation_key_id'=>$key ) );
            
        }

        return $key;
    }

    public function get($id) {
    	$sth = $this->db->prepare("SELECT c.c_id, c.user_id, u.name
            FROM conversation c INNER JOIN users u ON c.user_id=u.user_id
            WHERE c.c_id=:id LIMIT 1");
        $sth->execute( array(':id'=>$id) );

        return $sth->fetch( PDO::FETCH_ASSOC );
    }

    public function send() {
        // 
    	$data = array(
    		'text' => isset($_REQUEST['text'])? $this->fn->q('text')->strip_tags_br( trim($_REQUEST['text'])):'',
    		'id' => isset($_REQUEST['id'])? $_REQUEST['id']:'new',
            'to_id' => isset($_REQUEST['to'])? $_REQUEST['to']:null,
    		'time' => date('Y-m-d H:i:s'),
	    	'form_id' => $this->me['conversation_key_id'],
    	);

        if( empty( $data['to_id'] ) ){
            return array('error' => true, 'error_message' => 'Error Send To ...');
            die;
        }

        if( $data['id']=='new' ){
            $this->db->insert('conversation_key', array('obj_type'=>'member', 'obj_id'=>$data['to_id']));
            $data['key_id'] = $this->db->lastInsertId();
            $this->query('member')->update( $data['to_id'], array('m_conversation_key_id'=>$data['key_id']) );

            $this->db->insert('conversation', array(
                'c_key_id'=> $data['key_id'],
                'created' => date('c'),
                'updated' => date('c'),
            ));
            $data['id'] = $this->db->lastInsertId();
        }

        if( empty( $data['form_id'] ) ) {
            $data['form_id'] = $this->getKey($this->me['user_id']);
        }

    	$dataPost = array(
    		'c_id_fk' => $data['id'],
    		'text' => $data['text'],
    		'c_key_id' => $data['form_id'],
    		'time' => $data['time']
    	);
    	
    	// check Latest
    	$data['latest'] = $this->latest($data['id']);
    	if( !empty($data['latest']) ){
            $data['key_id'] = $data['latest']['c_key_id'];
    		$difference = strtotime( $data['time'] ) - strtotime( $data['latest']['time'] );
    		if( $difference < 60 && $data['latest']['c_key_id']==$data['form_id']){ // 13 วิ
    			$data['hasUpdate'] = true;
    			$data['cr_id'] = $data['latest']['cr_id'];
    		}
    	}

    	// send Msg
    	$dataPost['text'] = $this->fn->q('text')->strip_tags_html($data['text']);
    	if( !empty($data['hasUpdate']) ){

    		$data['text'] = $data['latest']['text'].'<br />'.$dataPost['text'];
    		$dataPost['text'] = $data['text'];
	    	$this->db->update('conversation_reply', $dataPost, "`cr_id`={$data['latest']['cr_id']} AND c_id_fk={$data['id']}");
    	}
    	else{
    		$this->db->insert('conversation_reply', $dataPost);
	    	$data['cr_id'] = $this->db->lastInsertId();
    	}

    	// update room
    	$this->db->update('conversation', array('updated' => $data['time'] ), "`c_id`={$data['id']}");

    	// get users join 
        if( empty($data['key_id']) ){
            $latest = $this->latest($data['id']);
            $data['key_id'] =  $latest['c_key_id'];
        }

        $data['ids'][] = $data['key_id'];

        $data['ids'][] = $data['form_id'];
       /* $form = $this->query('users')->get( $data['user_id'] );
        $data['name'] = $form['name'];

    	$data['users'][] = $form;
    	$data['users'][] = $this->query('users')->get( $data['send_to_id'] );*/
    	/*foreach ($this->query('messages')->getUserAdmin() as $key => $user) {
    		$data['users'][] = $user;
    	}*/

    	return $data;
    }

    public function latest($id) {
    	$sth = $this->db->prepare("SELECT * FROM conversation_reply WHERE c_id_fk=:id ORDER BY time DESC LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );
        return $data = $sth->fetch( PDO::FETCH_ASSOC );
    }

    // 
    public function nav_recent($options = array()) {

        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'desc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'c.updated',

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),

            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $field = "  c.c_id as id, c.c_key_id as key_id, c.updated
                    , m_name as title, m_id, m_phone_number as phone_number, m_email as email, m_username as username
            ";
        $sql = "conversation c LEFT JOIN member m ON c.c_key_id=m.m_conversation_key_id";

        $where_str = "c.created<=:time";
        $where_arr = array( ':time' => $date );
        
        $arr['total'] = $this->db->count($sql, $where_str, $where_arr);

        // lists
        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] = $this->recentBuildFrag( $this->db->select("SELECT {$field} FROM {$sql} WHERE {$where_str} {$orderby} {$limit}", $where_arr) );

        // echo "SELECT {$field} FROM {$sql} WHERE {$where_str} {$orderby} {$limit}"; die;

        // options
        if (($options['pager'] * $options['limit']) >= $arr['total']) $options['more'] = false;
        $arr['options'] = $options;

        return $arr;
    }

    public function recentBuildFrag($results) {
        $data = array();
        foreach ($results as $key => $value) {
            $data[] = $this->recentConvert($value);
        }
        return $data;
    }
    public function recentConvert($result) {

        $result['latest'] = $this->latest( $result['id'] );

        return $result;
    }

    public function nav_person($options = array()) {
        
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,

            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'desc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'m_name',

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),

            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);


        $field = "  m_conversation_key_id as key_id, m_name as title, m_id, m_phone_number as phone_number, m_email as email, m_username as username
            ";
        $sql = "member m";

        $where_str = "m.m_created<=:time";
        $where_arr = array( ':time' => $date );
        
        $arr['total'] = $this->db->count($sql, $where_str, $where_arr);

        // lists
        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $arr['lists'] = $this->personBuildFrag( $this->db->select("SELECT {$field} FROM {$sql} WHERE {$where_str} {$orderby} {$limit}", $where_arr) );

        // echo "SELECT {$field} FROM {$sql} WHERE {$where_str} {$orderby} {$limit}"; die;

        // options
        if (($options['pager'] * $options['limit']) >= $arr['total']) $options['more'] = false;
        $arr['options'] = $options;
        
        // print_r($arr); die;
        return $arr;
    }

    public function personBuildFrag($results)  {
        $data = array();
        foreach ($results as $key => $value) {
            $data[] = $this->personConvert($value);
        }
        // print_r($data); die;
        return $data;
    }
    public function personConvert($result) {

        if( !empty( $result['key_id'] ) ){
            
            $sth = $this->db->prepare("SELECT c_id as id, c_key_id as key_id, updated FROM conversation WHERE c_key_id=:id LIMIT 1");
            $sth->execute( array(
                ':id' => $result['key_id']
            ) );

            if( $sth->rowCount()==1 ){
                $result = array_merge($result, $sth->fetch( PDO::FETCH_ASSOC )) ;
            }

        }

        return $result;
    }
}