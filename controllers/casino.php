<?php

class Casino extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->elem('body')->addClass('col-x');
        $this->view->currentPage = 'casinoOnline';
    }
    
    public function index() {

        $this->error();
    }

    public function online() {

        
        $this->view->render('games/casino/online');
    }

    public function gclub(){
        
        $this->view->render('games/casino/gclub');
    }

    public function royal1688() {
        $this->view->render('games/casino/royal1688');
    }

    public function ruby888() {
        
        $this->view->render('games/casino/ruby888');
    }

    public function holiday()
    {
        $this->view->render('games/casino/holiday');
    }

    public function genting()
    {
        $this->view->render('games/casino/genting');
    }
    

}