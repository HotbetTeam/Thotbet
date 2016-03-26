<?php

class Sport extends Controller {

    function __construct() {
        parent::__construct(); 
        
        $this->view->currentPage = 'sportOnline';
        $this->view->elem('body')->addClass('col-x');
    }
    
    public function index() {
        $this->error();
    }

    public function online() {
        $this->view->render('games/sport/online');
    }

    public function sbobet() {
        $this->view->render('games/sport/sbobet');
    }

    public function ibcbet() {
        $this->view->render('games/sport/ibcbet');
    }

    public function winningft()
    {
        $this->view->render('games/sport/winningft');
    }

    public function threembet()
    {
        $this->view->render('games/sport/threembet');
    }

    public function mix8888()
    {
        $this->view->render('games/sport/mix8888');
    }

    public function bet855()
    {
        $this->view->render('games/sport/bet855');
    }
}