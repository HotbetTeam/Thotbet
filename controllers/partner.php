<?php

class Agent extends Controller {

    function __construct() {
        parent::__construct();
        $this->view->formatPage = 'agent';
    }
   
}
