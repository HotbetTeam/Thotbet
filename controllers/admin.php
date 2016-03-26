<?php

class Admin extends Controller {

    function __construct() {
        parent::__construct();

        // $this->view->formatPage = 'manage';
        
    }
    
    public function index() {

        $this->error();
        /*$nav[] = array( 'text' => 'Admin' );
        $this->view->nav = $nav;*/

        // $this->view->hasLeftCol = 'admin/users/leftCol/display';
        /*$this->view->currentPage = "admin";

        $this->view->data = $this->model->query('admin')->lists();
        $this->view->render('admin/lists/display');*/
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