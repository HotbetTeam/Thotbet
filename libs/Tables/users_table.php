<?php
	
class Users_Table extends Model {
	
	public function __construct() {
		parent::__construct();
	}

    public function lists($options=array()){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $width = array( ':time' => $date );

        $sth = $this->db->prepare("SELECT COUNT(*) FROM users WHERE created<=:time");
        $sth->execute( $width );
        $arr['total'] = $sth->fetchColumn();

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT 
            user_id, name, email, phone_number,access_id, u_conversation_key_id as conversation_key_id
            FROM users WHERE created<=:time 
            ORDER BY created DESC {$limit}", $width ) );

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
        
        $sth = $this->db->prepare("SELECT user_id,email,name,phone_number,access_id, u_conversation_key_id as conversation_key_id FROM users WHERE user_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) );
        } return array();
    }

    public function convert($result){
        $result = array_merge($result, $this->getMember($result['user_id']));

        // print_r($result); die;

        // $result['sum_point'] = isset($result['sum_point']) ?$result['sum_point']:0;
        
        $result['image_url'] = IMAGES.'avatar/error/admin.png';
        return $result;
    }

    public function getMember( $id ){
        
        $sth = $this->db->prepare("SELECT m_id,sum_point FROM member WHERE user_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $sth->fetch( PDO::FETCH_ASSOC );
        } return array();
    }

    public function login($user, $pess){

        $sth = $this->db->prepare("SELECT user_id as id FROM users WHERE (email=:login AND pass=:pass) OR (phone_number=:login AND pass=:pass)");

        $sth->execute( array(
            ':login' => $user,
            ':pass' => Hash::create('sha256', $pess, HASH_PASSWORD_KEY)
        ) );

        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            return $fdata['id'];
        } return false;
    }

    public function loginSocial($data) {

        $sth = $this->db->prepare("SELECT u.user_id
            FROM users u LEFT JOIN social s ON u.user_id=s.user_id
            WHERE social_type=:type OR social_id=:id LIMIT 1");

        $sth->execute( array(
            ':type' => $data['type'],
            ':id' => $data['id']
        ) );

        // update
        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            $this->db->update('social', array('updated'=>date('c')), "social_type='{$data['type']}' AND social_id='{$data['id']}' AND user_id={$fdata['user_id']}");
            return $fdata;
        }

        // new
        else{

            // user
            if( $this->is_user( $data['email'] ) ){
                // login
                $sth = $this->db->prepare( "SELECT user_id,display FROM users WHERE email=:login" );
                $sth->execute( array(
                    ':login' => $data['email']
                ) );

                if ($sth->rowCount()==1) {

                    $user = $sth->fetch( PDO::FETCH_ASSOC );

                    if( !empty($user['avatar']) ){
                        $user['profile_image_url'] = IMAGES_AVATAR.$user['avatar'];
                    }
                }
            }

            if( empty( $user ) ){

                // new user
                $user = array(
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'display' => 'enabled',
                    // 'language_code' => $data['locale'],
                    'access_id' => 2
                );

                $this->insert($user);

                // 
                $member = array( 'user_id' => $user['user_id'] );
                $this->query('member')->insert($member);

                // update picture
                /*if( empty( $user['profile_image_url'] ) && !empty($data['image_url']) ){
                    // upload avatar

                    //add time to the current filename
                    $url = $data['image_url'];
                    $name = basename( $url );
                    $type = explode("?", strtolower(substr(strrchr($name,"."),1)) );
                    $name = "avatar_{$user['user_id']}_n.".$type[0];
                    $path = WWW_IMAGES_AVATAR.$name;
                    
                    $upload = file_put_contents("$path",file_get_contents($url));
                    
                    $user['profile_image_url'] = IMAGES_AVATAR.$name;
                    $this->update($user['user_id'], array('avatar'=>$name));
                }*/
            }

            // social
            if( $this->db->count("social", "user_id=:uid AND social_type=:type AND social_id=:id", array(
                ':uid' => $user['user_id'],
                ':id' => $data['id'],
                ':type' => $data['type'],
            )) == 0 ){

                $this->db->insert('social', array(
                    'user_id' => $user['user_id'],
                    'social_id' => $data['id'],
                    'social_type' => $data['type'],
                    'updated' => date('c')
                ));
            }
            
            return $user;
        }
    }

    public function is_user($text){
        return $this->db->count("users", "email='{$text}' OR phone_number='{$text}'");
    }

    public function insert(&$data){

        $data['created'] = date('c');
        $data['updated'] = date('c');

        if( !empty($data['pass']) ){
            $data['pass'] = Hash::create('sha256', $data['pass'], HASH_PASSWORD_KEY);
        }
        
        $this->db->insert('users', $data);
        $data['user_id'] = $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $data['updated'] = date('c');
        $this->db->update('users', $data, "`user_id`={$id}");
    }

    public function delete($id)
    {
        $this->db->delete('users', "`user_id`={$id}");
    }

   
}