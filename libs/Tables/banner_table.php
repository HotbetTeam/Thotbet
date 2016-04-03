<?php
    
class Banner_Table extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function update(&$data) {


    	$sth = $this->db->prepare("SELECT * FROM banner WHERE banner_width=:width AND banner_height=:height LIMIT 1");
        $sth->execute( array(
            ':width'=>$data['banner_width'], 
            ':height' => $data['banner_height']
        ) );

        if( $sth->rowCount()==0 ){
            $this->db->insert('banner', $data);
    		$data['banner_id'] = $this->lastInsertId();
        }
        else{
        	$fdata = $sth->fetch( PDO::FETCH_ASSOC );
        	$data['banner_id'] = $fdata['banner_id'];

        	$this->db->update('banner', $data, "`banner_id`={$data['banner_id']}");
        }

    }

    public function lists() {
    	
    	return $this->db->select("SELECT * FROM banner ORDER BY banner_id ASC");
    }

}

