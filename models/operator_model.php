<?php

class Operator_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert($data) {
        $this->db->insert('operator', $data);
        return  $this->db->lastInsertId();
    }
    public function QueryOperator() {
       return $this->db->select('SELECT * FROM operator  AS o LEFT JOIN users AS u ON u.user_id = op_user_id ORDER BY op_id LIMIT 0,100 ');
    }
    
    function update($id, $data){
        $this->db->update('operator', $data, "op_id={$id}");
    }
    
    function delete($id){
        $this->db->delete('operator', "op_id={$id}");
    }

}
