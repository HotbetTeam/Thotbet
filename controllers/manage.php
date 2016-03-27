<?php

class Manage extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->theme = 'manage';

        $this->view->currentPage = "manage";
        $this->view->elem('body')->addClass('hidden-tobar settings-page');
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

    public function index() {
        header('Location:' . URL .'manage/users/admin');
    }

    public function member($section="index"){

        if( !in_array($section, array('index', 'points', 'level')) ){
            $this->error();
        }

        // get data point
        if( $section=='points' ){
            $this->view->post = $this->model->query('member')->getPoint();
        }


        if( $section=='level' ){

            // print_r($this->model->query('member')->listsLevel()); die;
            $this->view->results = $this->model->query('member')->levelLists();
        }

        $this->view->section = "member/{$section}";
        $this->view->render("manage/display");
    }

    public function users($section="index") {

        if( !in_array($section, array('index', 'admin', 'operator')) ){
            $this->error();
        }

        if( $section=='admin' ){
            $access_id = 1;
            $this->view->access_id = $access_id;
            $this->view->results = $this->model->query('admin')->lists( array('access_id'=>$access_id) );
        }

        if($section=='operator'){
            $access_id = 3;
            $this->view->access_id = $access_id;
            $this->view->results = $this->model->query('admin')->lists( array('access_id'=>$access_id) );
        }

        $this->view->section = "users/{$section}";
        $this->view->render("manage/display");
    }

    public function agent($id=null) {

        $this->view->currentPage = "agent";
        if( !empty($id) ){

            $item = $this->model->query('agent')->get( $id );
            if(empty($item)) $this->error();

            $this->view->results = $this->model->query('agent')->member( $id );
            $this->view->status = isset($_REQUEST['status']) ? $_REQUEST['status']: null;
            $this->view->statusCounts = $this->model->query('member')->statusCounts();

            if( $this->format=='json' ){
                $this->view->render('manage/agent/profile/lists/json');
            }
            else{

                Session::init();                          
                Session::set('isPushedLeft', false);
                $this->view->elem('body')->addClass('is-overlay-left page-listpage');

                $this->view->item = $item;
                $this->view->render('manage/agent/profile/display');
            }
        }
        else{
            $this->view->results = $this->model->query('agent')->lists();

            if( $this->format=='json' ){
                $this->view->render('manage/agent/lists/json');
            }else{
                $this->view->render("manage/agent/lists/display");
            }
        }
        
    }

}