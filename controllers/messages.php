<?php

class Messages extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function index( $id=null ){

        if( !empty($id) ){
            
        }else if( $id=='new' ){
            
        }
        // sleep(3);
    	echo json_encode( $this->model->lists() );
    }

    public function send() {
        // sleep(3);
    	echo json_encode( $this->model->send() );
    }
}