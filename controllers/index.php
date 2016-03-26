<?php

class Index extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->formatPage = 'home';
    }
    
    function index() {

    	$this->view->currentPage = 'home';
    	$this->view->elem('body')->addClass('home');
    	// $this->view->js('casino');
        $this->view->render('index/home');
    }
    
}