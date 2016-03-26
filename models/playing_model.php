<?php

class Playing_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getMember($user) {
        $sth = $this->db->prepare("SELECT m_id, m_point as point FROM member WHERE m_username=:user LIMIT 1");
        $sth->execute( array(
            ':user' => $user,
        ) );

        if( $sth->rowCount()==1 ){
            return $sth->fetch( PDO::FETCH_ASSOC );
        }
        else{
            return false;
        }
    }

    public function checkDate($mid, $date) {

        $sth = $this->db->prepare("SELECT pl_id FROM playing WHERE pl_m_id=:id AND pl_date=TIMESTAMP(:d) LIMIT 1");
        $sth->execute( array(
            ':id' => $mid,
            ':d' => $date
        ) );

        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            return $fdata['pl_id'];
        }
        else{
            return false;
        } 
    }

    public function selectMember($menberCode,$date) {
        $res = $this->db->select($sql = "SELECT * FROM playing WHERE pl_menber='$menberCode' AND pl_date='$date' LIMIT 0,1");
        return $res;
    }

    public function updateScore($data, $where) {
        $this->db->update('playing', $data, $where);
    }
     public function InsertScore($data) {
        $this->db->insert('playing', $data);
    }

}
