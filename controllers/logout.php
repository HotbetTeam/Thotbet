<?php

class Logout extends Controller {

    function __construct() {
        parent::__construct();

    }

    public function index(){
    	if( empty($this->me) ){
			header('location:' . URL);
		}

        /*Session::init();
        Session::destroy();*/

        $url = !empty($_REQUEST['next'])
            ? $_REQUEST['next']
            : URL;

        Cookie::clear( COOKIE_KEY );
        header('location:' . $url);
    }

    public function admin() {
        
        $url = URL.'manage';
        if( empty($this->me) ){
            header('location:' . $url );
        }
        
        $url = !empty($_REQUEST['next'])
            ? $_REQUEST['next']
            : $url;

        Cookie::clear( COOKIE_KEY_ADMIN );
        header('location:' . $url);
    }

    public function partner() {
        $url = URL.'partner';

        if( $this->format == 'json' ){
            $this->view->render('partner/dialog/confirm_logout');
            exit;
        }

        if( empty($this->me) ){
            header('location:' . $url );
        }

        $url = !empty($_REQUEST['next'])
            ? $_REQUEST['next']
            : $url;

        Cookie::clear( COOKIE_KEY_PARTNER );
        header('location:' . $url);

    }

}