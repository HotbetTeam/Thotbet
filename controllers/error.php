<?php

class Error extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    function index() {
    	$this->_errorPathUnavailable = true;
    	$this->error();
    }

}