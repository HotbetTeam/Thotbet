<?php

class Partner_Table extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**/
    /* config Data */

    public $_field = " * ";
    public $_table = "partner";

    public function options($options = array()) {
        return array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'desc',
            'sort_field' => isset($_REQUEST['sort_field']) ? $_REQUEST['sort_field'] : 'partner_created',
            'status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : null,
            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
            'more' => true
                ), $options);
    }

    /* manage */

    public function insert(&$data) {

        $data['partner_created'] = date('c');
        $data['partner_updated'] = date('c');

        $this->db->insert('partner', $data);
        $data['partner_id'] = $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $data['partner_updated'] = date('c');
        $this->db->update('partner', $data, "`partner_id`={$id}");
    }

    public function delete($id) {
        $this->db->delete('partner', "`partner_id`={$id}");
    }

    // ดึงข้อมูลครั้งละ หลายๆ แถว
    public function lists($options = array()) {
        $options = $this->options($options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "partner_created<=:time";
        $where_arr = array(':time' => $date);

        if( !empty($options['q']) ){
            $where_str .= !empty( $where_str ) ? " AND ":'';
            $where_str .= "(partner_name LIKE :q OR partner_email=:qFull OR partner_tel=:qFull)";
            $where_arr[':q'] = "%{$options['q']}%";
            $where_arr[':qFull'] = "{$options['q']}";
        }

        $arr['total'] = $this->db->count($this->_table, $where_str, $where_arr);

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby($options['sort_field'], $options['sort']);
        $where_str = !empty($where_str) ? "WHERE {$where_str}" : '';
        $arr['lists'] = $this->buildFrag($this->db->select("SELECT {$this->_field} FROM {$this->_table} {$where_str} {$orderby} {$limit}", $where_arr));

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        return $arr;
    }

    // ดึงข้อมูลครั้งละ 1 แถว
    public function get($id) {
        $sth = $this->db->prepare("SELECT {$this->_field} FROM {$this->_table} WHERE partner_id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id
        ));

        if ($sth->rowCount() == 1) {
            return $this->convert($sth->fetch(PDO::FETCH_ASSOC));
        } return array();
    }

    // แปลงข้อมูล
    public function buildFrag($results) {
        $data = array();
        foreach ($results as $key => $value) {
            if (empty($value))
                continue;
            $data[] = $this->convert($value);
        }

        return $data;
    }

    public function convert($data) {

        $data['url'] = URL . 'manage/partner/' . $data['partner_id'];
        $data['image_url'] = IMAGES . 'avatar/error/admin.png';


        return $data;
    }

    /* join */
    /* member */
    public function member($aid, $options = array(), $hasAdmin=false) {
        $options = $this->query('member')->options($options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $where_str = "m_created <= :d";
        $where_arr[':d'] = $date;

        $where_str .= " AND m_partner_id=:partnerID";
        $where_arr[':partnerID'] = $aid;
        
        if( $hasAdmin ){
            $where_str .= " AND m_status!='cancel'";
        }
        else{
            $where_str .= " AND m_status!='verify' AND m_status!='cancel'";
        }

        if( !empty($options['q']) ){
            $where_str .= !empty( $where_str ) ? " AND ":'';
            $where_str .= "(m_name LIKE :q OR m_username=:qFull)";
            $where_arr[':q'] = "%{$options['q']}%";
            $where_arr[':qFull'] = "{$options['q']}";
        }

        $arr['total'] = $this->db->count($this->query('member')->_table, $where_str, $where_arr);

        $limit = $this->limited($options['limit'], $options['pager']);
        $orderby = $this->orderby($options['sort_field'], $options['sort']);
        $where_str = !empty($where_str) ? "WHERE {$where_str}" : '';
        $arr['lists'] = $this->query('member')->buildFrag($this->db->select("SELECT {$this->query('member')->_field} FROM {$this->query('member')->_table} {$where_str} {$orderby} {$limit}", $where_arr));

        // echo "SELECT {$this->query('member')->_field} FROM {$this->query('member')->_table} {$where_str} {$orderby} {$limit}"; die;

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }

        $arr['options'] = $options;
        return $arr;
    }

    public function getMember($mid, $aid) {

        $sth = $this->db->prepare("SELECT {$this->query('member')->_field()} FROM {$this->query('member')->_table()} WHERE m_partner_id=:aid AND m_id=:mid LIMIT 1");
        $sth->execute(array(
            ':aid' => $aid,
            ':mid' => $mid
        ));

        if ($sth->rowCount() == 1) {
            return $this->query('member')->convert($sth->fetch(PDO::FETCH_ASSOC));
        } return array();
    }

    public function getCountStatus($status){
        return $this->db->count('member', "m_status=:t", array(':t'=>$status));
    }

    public function joinMember($id, $mid) {
        $partner = $this->get( $id );

        if( !empty($partner) ){

            $this->update($id, array('partner_total_member' => ++$partner['partner_total_member'] ));
            $this->query('member')->update( $mid, array('m_partner_id'=>$id) );
        }
    }
    public function delMember($id, $mid) {
        $partner = $this->get( $id );

        if( !empty($partner) ){
            $this->update($id, array('partner_total_member' => --$partner['partner_total_member'] ));
        }
    }

    /* ตรวจสอบข้อมูล */

    /* login */

    public function login($user, $pess) {

        $sth = $this->db->prepare("SELECT partner_id as id FROM partner WHERE (partner_email=:login AND partner_password=:pass) OR (partner_tel=:login AND partner_password=:pass)");

        $sth->execute(array(
            ':login' => $user,
            ':pass' => $pess
        ));

        if ($sth->rowCount() == 1) {
            $fdata = $sth->fetch(PDO::FETCH_ASSOC);
            return $fdata['id'];
        } return false;
    }

    /* ข้อมูลซ่ำ  */

    public function duplicate($text) {
        return $this->db->count("partner", "partner_email='{$text}' OR partner_tel='{$text}'");
    }

}
