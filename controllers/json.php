<?php

class Json extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function navTrigger() {
        
        if( isset($_REQUEST['status']) ){
            Session::init();                          
            Session::set('isPushedLeft', $_REQUEST['status']);
        }
        else{
            $this->error();
        }
    }
    
}
