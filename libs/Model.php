<?php

class Model {

    function __construct() {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $this->fn = new _function();
    }

    // private query protected
    private $_query = array();

    // Public query
    public function query( $table=null ){
        
        if(array_key_exists($table, $this->_query)==false){
            require_once "Tables/{$table}_table.php";
            $tableName = $table . '_Table';
            $this->_query[$table] = new $tableName;
        }

        return $this->_query[$table];
        
    }
    protected function limited($limit=0, $pager=1, $del=0){
        return "LIMIT ".((($pager*$limit)-$limit)-$del) .",". $limit;
    }
    protected function orderby($field, $sort='DESC'){
        $sort = strtoupper($sort);
        return "ORDER BY ".( $sort=='rand'  ? "rand()": "{$field} {$sort}" );
    }

    /* query table to object */

    //
    public function getUser( $uid=null ){
        return $this->query( "users" )->get( $uid );
    }
}