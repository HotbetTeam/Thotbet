<?php

class Search extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    public function index() {
        
        $q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
        $objects['places'] = array('type'=>'places','name'=>'ร้าน'); 
        $objects['foods'] = array('type'=>'foods','name'=>'เมนูอาหาร');

        $this->view->data = $this->model->results($objects, $q);
        $this->view->render('index/search');   
    }

    public function q(){
    	$q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
        $objects['places'] = array('type'=>'places','name'=>'ร้าน');    	
        $objects['foods'] = array('type'=>'foods','name'=>'เมนูอาหาร');

    	echo json_encode( $this->model->results($objects, $q) );
    }
    public function foods()
    {
        $q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
        $objects['type_food'] = array('type'=>'type_food','name'=>'ประเภทเมนูอาหาร');

        echo json_encode( $this->model->results($objects, $q) );
    }
    public function places()
    {
        $q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
        $objects['type_place'] = array('type'=>'type_place','name'=>'ประเภทร้าน');        

        echo json_encode( $this->model->results($objects, $q) );
    }

    /*public function groups(){
        $q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
        $objects['groups'] = array('type'=>'group','name'=>'กลุ่ม');
        echo json_encode( $this->model->results($objects, $q) );
    }

    public function users(){
        $q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
    	$objects['users'] = array('type'=>'user','name'=>'บุคคล');
        echo json_encode( $this->model->results($objects, $q) );
    }*/

    /*public function projects(){
        $q = isset( $_REQUEST['q'] )? $_REQUEST['q']: "";
        $objects['projects'] = array('type'=>'project','name'=>'โครงงาน');
        echo json_encode( $this->model->results($objects, $q) );
    }*/

}