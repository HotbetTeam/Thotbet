<?php

class Join extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    public function index() {

    	$this->view->elem('body')->addClass('col-x');
    	$this->view->currentPage = 'join';
        $this->view->render('about/join');
    }

}