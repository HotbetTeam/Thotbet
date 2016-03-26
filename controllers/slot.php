<?php

class Slot extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    public function index() {

    	$this->view->elem('body')->addClass('col-x');
    	$this->view->currentPage = 'slot';
        $this->view->render('games/slot/display');
    }
}