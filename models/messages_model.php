<?php

class Messages_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

   	public function lists( $options = array() ) {
   		$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),

            'id' => isset($_REQUEST['id'])? $_REQUEST['id']:$this->search(),

            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);


        $where_str = "c_id_fk=:id AND time<=TIMESTAMP(:d)";
   		$where = array( ':id'=>$options['id'], ':d' => $date ); //

   		$arr['total'] = $this->db->count('conversation_reply', $where_str,$where );
        //  

   		$limit = $this->limited( $options['limit'], $options['pager'] );
   		$arr['lists'] = $this->db->select("SELECT * FROM conversation_reply WHERE {$where_str} ORDER BY time DESC {$limit}", $where);

   		if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

   		$arr['options'] = $options;

        if( $options['pager']==1 ){
            $lists[] = array(
                'text' => '<p>การตอบบทสนทนา<br> พนักงานของเราจะให้บริการท่านในไม่ช้า กรุณารอสักครู่ ขอบคุณค่ะ</p>',
                'time' => $date,
            ); 
            /* ตอนนี้ท่านอยู่ในคิวลำดับที่ 1 รอประมาณ 1 นาที ขอบคุณสำหรับการรอของท่าน */
            
            foreach ($arr['lists'] as $key => $value) {
                $lists[] = $value;
            }
            $arr['lists'] = $lists;
        }

   		return $arr;
   	}

    public function search($id=null){

    	$id = !empty($id)? $id: $this->me['m_id'];

    	$sth = $this->db->prepare("SELECT c.c_id 
            FROM conversation c INNER JOIN conversation_key k ON k.conversation_key_id=c.c_key_id 
            WHERE obj_id=:id AND obj_type=:type LIMIT 1");
        $sth->execute( array(
            ':type' => 'member',
            ':id' => $id
        ) );

        $roomId = 0;
        if( $sth->rowCount()==1 ){
            $data = $sth->fetch( PDO::FETCH_ASSOC );
            $roomId = $data['c_id'];
        }

        return $roomId;
    }

    public function send() {

    	$data = array(
    		'text' => isset($_REQUEST['text'])? $_REQUEST['text']:'',
    		'id' => isset($_REQUEST['id'])? $_REQUEST['id']:null,
    		'c_key_id' => $this->me['conversation_key_id'],
	    	'time' => date('Y-m-d H:i:s'),
	    	'evt' => 'insert'
    	);

    	if( empty($data ['text']) ){
    		$data = array(
    			'error' =>  1,
    			'message' => 'ไม่มีข้อความ'
    		);
    	}
    	else{

    		if( empty($data['id']) ){

    			$data['id'] = $this->search();

    			if( empty($data['id']) ){

                    $this->db->insert('conversation_key', array( 
                        'obj_type' => 'member',
                        'obj_id' => $this->me['m_id']
                     ) );
                    $data['c_key_id'] = $this->db->lastInsertId();

                    $this->query('member')->update( $this->me['m_id'], array('m_conversation_key_id' => $data['c_key_id']) );
                    $this->me['conversation_key_id'] = $data['c_key_id'];

    				// 
		    		$this->db->insert('conversation', array(
		    			'c_key_id'=> $data['c_key_id'],
                        'created' => date('Y-m-d H:i:s'),
		    			'updated' => date('Y-m-d H:i:s')
		    		));
		    		$data['id'] = $this->db->lastInsertId();
    			}
	    		
	    		// set user
	    	}

	    	$data['text'] = $this->fn->q('text')->strip_tags_html($data['text']);
	    	$dataPost = $data;
	    	$dataPost['c_id_fk'] = $data['id'];
	    	unset($dataPost['id']);
	    	unset($dataPost['evt']);

	    	$latest = $this->latest($data['id']);
	    	if( !empty($latest) ){

	    		$difference = strtotime( $data['time'] ) - strtotime( $latest['time'] );
	    		if( $difference < 60 && $latest['c_key_id']==$this->me['conversation_key_id']){ // 13 วิ
	    			$data['evt'] = 'update';
	    			$data['cr_id'] = $latest['cr_id'];
	    		}

	    	}

            // update Text
	    	if( $data['evt']=='update' ){

	    		$data['text'] = $latest['text'].'<br />'.$this->fn->q('text')->strip_tags_html($data['text']);
	    		$dataPost['text'] = $data['text'];
	    		$this->db->update('conversation_reply', $dataPost, "`cr_id`={$data['cr_id']} AND c_id_fk={$data['id']}");

	    	}
	    	else{
	    		$this->db->insert('conversation_reply', $dataPost);
	    		$data['cr_id'] = $this->db->lastInsertId();
	    	}

            // update room
            $this->db->update('conversation', array('updated' => $dataPost['time'] ), "`c_id`={$dataPost['c_id_fk']}");

	    	// get user

            // key ที จะรับ 
            $data['ids'][] = $this->me['conversation_key_id']; 

            // แจ้งเตือน ผู้ที่ต้องรับ เป็น admin
            foreach ($this->query('messages')->getUserAdmin() as $key => $user) {
                if( !empty($user['conversation_key_id']) ){
                    $data['ids'][] = $user['conversation_key_id'];
                }
                
            }
    	}

    	// die;
    	
    	return $data;
    }

    public function latest($id)
    {
    	$sth = $this->db->prepare("SELECT * FROM conversation_reply WHERE c_id_fk=:id ORDER BY time DESC LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );
        return $data = $sth->fetch( PDO::FETCH_ASSOC );
           
    }
}