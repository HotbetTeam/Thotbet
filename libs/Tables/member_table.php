<?php
    
class Member_Table extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    /**/
    /* config Data */
    public $_field = "  m_username as username
                        , m_password as password
                        , game_user, game_pass
                        , m_created as created
                        , m_updated as updated
                        , m_point as point
                        , m_point_show as point_show
                        , m_status as status 
                        , m_id
                        , m_name as name
                        , m_email as email
                        , m_phone_number as phone_number
                        , m_level_id as level_id
                        , lev_name as level_name
                        , m_note as note
                        , m_partner_id as partner_id
                        , m_conversation_key_id as conversation_key_id";

    public $_table = "member m INNER JOIN member_level l ON m_level_id=l.lev_id";
    public function options($options=array()) {
        return array_merge( array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:50,
            
            'sort' => isset($_REQUEST['sort'])? $_REQUEST['sort']:'desc',
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'m_created',
            
            'status' => isset($_REQUEST['status'])? $_REQUEST['status']:null,

            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
    }

    public function lists($options=array()){
        $options = $this->options( $options );
        // 
        $where_str = "m_created<=:time";
        $where_arr = array( ':time' => date('Y-m-d H:i:s', $options['time']) );

        $where_str .= empty($options['status']) || $options['status']=='all'
            ? " AND (m_status!='verify' AND m_status!='cancel')"
            : " AND m_status='{$options['status']}'";
        // 
        $arr['total'] = $this->db->count( $this->_table, $where_str,  $where_arr );

        // 
        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby( $options['sort_field'], $options['sort'] );
        $where_str = !empty($where_str) ? "WHERE {$where_str}":'';
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr ) );
        // echo "SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}"; die;
        // 
        if( ($options['pager']*$options['limit']) >= $arr['total'] ) $options['more']=false;
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

        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE m_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        if( $sth->rowCount()==1 ){
            return $this->convert( $sth->fetch( PDO::FETCH_ASSOC ) );
        } return array();
    }
    public function convert($data){

        switch ($data['status']) {
            case 'pause':
                $data['status_str'] = 'หยุดการใช้งาน';
                break;

            case 'play':
                $data['status_str'] = 'เปิดการใช้งาน';
                break;

            case 'cancel':
                $data['status_str'] = 'ยกเลิก';
                break;

            case 'verify':
                $data['status_str'] = 'รอการอนุมัติ';
                break;
            
            
            default:
                $data['status_str'] = "";
                break;
        }


        if( !empty($data['partner_id']) ){
            $data['partner'] = $this->query('partner')->get( $data['partner_id'] );
        }

        // $data['point_str'] = round($data['point'], 0, PHP_ROUND_HALF_DOWN);
        $point_str = explode('.', $data['point']);
        $data['point_str'] = $point_str[0];

        $point_show_str = explode('.', $data['point_show']);
        $data['point_show_str'] = $point_show_str[0];

        $data['url'] = URL."member/{$data['m_id']}";
        return $data;
    }


    /**/
    /* login */
    public function login($user, $pass) {

        $sth = $this->db->prepare("SELECT m_id as id FROM member WHERE (m_username=:login AND m_password=:pass) OR (m_email=:login AND m_password=:pass) OR (m_phone_number=:login AND m_password=:pass)");
        $sth->execute( array(
            ':login' => $user,
            ':pass' => $pass
        ) );

        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            return $fdata['id'];
        } return false;
    }
    public function is_user($text) {
        return $this->db->count("member", "m_username='{$text}' OR m_email='{$text}' OR m_phone_number='{$text}'");
    }
    public function is_game_user($text)
    {
         return $this->db->count("member", "game_user='{$text}'");
    }
    public function loginSocial($data) {

        $sth = $this->db->prepare("SELECT m_id FROM social WHERE social_type=:type AND social_id=:id LIMIT 1");

        $sth->execute( array(
            ':type' => $data['type'],
            ':id' => $data['id']
        ) );

        // update
        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            
            $this->db->update('social', array('updated'=>date('c')), "social_type='{$data['type']}' AND social_id='{$data['id']}' AND m_id={$fdata['m_id']}");
            return $fdata['m_id'];
        }

        // new
        else{

            // login Email
            if( $this->is_user( $data['email'] ) ){
                // login
                $sth = $this->db->prepare( "SELECT m_id,status FROM member WHERE m_email=:login" );
                $sth->execute( array(
                    ':login' => $data['email']
                ) );

                if ($sth->rowCount()==1) {

                    $user = $sth->fetch( PDO::FETCH_ASSOC );

                    /*if( !empty($user['avatar']) ){
                        $user['profile_image_url'] = IMAGES_AVATAR.$user['avatar'];
                    }*/
                }
            }

            if( empty( $user ) ){

                // new user
                $user = array(
                    'm_name' => $data['name'],
                    'm_email' => $data['email'],
                    'm_status' => 'verify',
                    // 'language_code' => $data['locale'],
                    // 'access_id' => 2
                );

                $this->insert($user);
                $user['m_id'] = $this->db->lastInsertId();

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
            if( $this->db->count("social", "m_id=:uid AND social_type=:type AND social_id=:id", array(
                ':uid' => $user['m_id'],
                ':id' => $data['id'],
                ':type' => $data['type'],
            )) == 0 ){

                $this->db->insert('social', array(
                    'm_id' => $user['m_id'],
                    'social_id' => $data['id'],
                    'social_type' => $data['type'],
                    'updated' => date('c')
                ));
            }
            
            return $user['m_id'];
        }
    }

    public function autoGameUser() {
        $sth = $this->db->prepare("SELECT m_id as id FROM `member` ORDER BY m_id DESC");
        $sth->execute();

        $fdata = $sth->fetch( PDO::FETCH_ASSOC );
        return "Acde".sprintf("%03d", $fdata['id']);
    }

    /**/
    /* update */
    public function insert(&$data) {

        $data['m_created'] = date('c');
        $data['m_updated'] = date('c');

        if( empty($data['m_level_id']) ){
            $data['m_level_id'] = 1;
        }
        
        $this->db->insert('member', $data);
        $data['m_id'] = $this->db->lastInsertId();

        // เพิ่ม โดย Link Partner
        if( Cookie::get('partner_redirect') && !empty($data['m_id']) ){

            $this->query('partner')->joinMember(Cookie::get('partner_redirect'), $data['m_id']);
            Cookie::clear('partner_redirect');
        }
    }
    public function update($id, $data) {

        $data['m_updated'] = date('c');
        $this->db->update('member', $data, "`m_id`={$id}");
    }
    public function delete($id) {
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
        return $this->db->count('member', "m_status=:t", array(':t'=>$status));
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
        $field = "    pl_m_id as m_id
                    , m.game_user, m.game_pass
                    , pl_id
                    , pl_menber_point AS pl_menber_point_cal
                    , pl_actual_point AS pl_actual_point_cal
                    , pl_wagers
                    , pl_bet_amount
                    , pl_menber 
                    , pl_actual
                    , pl_date";
                    

        $sql = "playing AS p LEFT JOIN member m ON pl_m_id=m_id";

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
            'sort_field' => isset($_REQUEST['sort_field'])? $_REQUEST['sort_field']:'lev_score',
            
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
        $result['membership'] = $this->db->count('member', "m_level_id=:id", array(':id'=>$result['lev_id']));

        return $result;
    }

    public function setLevel(&$data){
        
        $data['lev_updated'] = date('c');
        $this->db->insert('member_level', $data);
        $data['lev_id'] = $this->db->lastInsertId();
    }
    public function getLevel($id) {

        $sth = $this->db->prepare("SELECT * FROM member_level WHERE lev_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );
        
        return $this->levelConvert( $sth->fetch( PDO::FETCH_ASSOC ) );
    }
    public function editLevel($id, $data) {
        $this->db->update('member_level', $data, "`lev_id`={$id}");
    }
    public function delLevel($id) {
        $this->db->delete('member_level', "`lev_id`={$id}");
    }
    public function upLevel($id, $poit, $level=1) {
        
        $data = $this->levelLists();
        foreach ($data['lists'] as $key => $value) {
            
            if( $poit >= $value['lev_score'] && $value['lev_has_auto'] ){
                $level = $value['lev_id'];
            }
        }

        $this->update($id, array('m_level_id'=>$level));
    }
}