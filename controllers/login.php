<?php

class Login extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function index(){
    	
    	// $this->view->render('index/login');
    	$this->login();
    }

    /*public function facebook(){

        if( empty($_POST) && $this->format!='json') $this->_error();

        $data = $_POST;
        if( !empty($data['email']) ){

            $arr['next'] = isset($_REQUEST['next'])? $_REQUEST['next']: null;
            $user = $this->model->query("users")->loginFB( $data );
            Cookie::set( COOKIE_KEY , $user['user_id'], time()+86400);

            if( !empty($arr['next']) ){
                $arr['url'] = $arr['next'];
            }
        }
        else{
            $arr['error'] = 'เรียกใช้ข้อมูลจาก Facebook ไม่เพียงพอ';
        }

        echo json_encode( $arr );
    }*/

    public function social()
    {

        if( empty($_POST) && $this->format!='json') $this->_error();

        $arr = array();
        $data = $_POST;
        $id = $this->model->query("member")->loginSocial($data);
        if( !empty($id) ){

            Cookie::set( COOKIE_KEY , $id, time()+86400); // 1 วัน
            if( isset($_REQUEST['next']) ){
                $arr['url'] = $_REQUEST['next'];
            }
        }
        else{

            $network = ucfirst(strtolower($data['type']));
            $arr['error'] = "รียกใช้ข้อมูลจาก {$network} ไม่เพียงพอ";
        }

        echo json_encode( $arr );
    }

    public function google(){
        if( empty($_POST) && $this->format!='json') $this->_error();

        $data = $_POST;
        if( !empty($data['email']) ){

            $arr['next'] = isset($_REQUEST['next'])? $_REQUEST['next']: null;
            $user = $this->model->query("users")->loginGoogle( $data );
            Cookie::set( COOKIE_KEY , $user['user_id'], time()+86400);

            if( !empty($arr['next']) ){
                $arr['url'] = $arr['next'];
            }
        }
        else{
            $arr['error'] = 'เรียกใช้ข้อมูลจาก Google ไม่เพียงพอ';
        }

        echo json_encode( $arr );
    }

}