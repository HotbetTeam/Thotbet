<?php

class Alert extends Controller {

    function __construct() {
        parent::__construct();

    }
    
    public function index() {

        $this->error();
    }

    public function up($section=null) {
        if( empty($this->me) || $this->format!='json' ) $this->error();

        if( $section == 'phone_number' ){
            $this->view->render('alert/up/phone_number');
        }
        
    }

    

}