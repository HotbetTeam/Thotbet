<?php

class Promotion extends Controller {

    function __construct() {
        parent::__construct();        
    }
    
    public function index() {

    	$this->view->elem('body')->addClass('col-x');
    	$this->view->currentPage = 'promotion';
        $this->view->render('promotion/display');
    }

}