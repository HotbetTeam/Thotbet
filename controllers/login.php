<?php

class Login extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function index(){
    	
        if (!empty($_POST)) {

            try {
                $form = new Form();

                $form   ->post('email')->val('is_empty')
                        ->post('pass')->val('is_empty');

                $form->submit();
                $post = $form->fetch();

                $id = $this->model->query('member')->login($post['email'], $post['pass']);

                if (!empty($id)) {

                    if (Cookie::get(COOKIE_KEY_ADMIN)) {
                        Cookie::clear( COOKIE_KEY_ADMIN );
                    }

                    if(Cookie::get(COOKIE_KEY_AGENT)){
                        Cookie::clear( COOKIE_KEY_AGENT );
                    } 

                    Cookie::set(COOKIE_KEY, $id, time() + (86400 * 30));

                    $url = !empty($_REQUEST['next']) ? $_REQUEST['next'] : $_SERVER['REQUEST_URI'];
                    header('Location: ' . $url);
                }
                else {
                    if (!$this->model->query('member')->is_user($post['email'])) {
                        $error['email'] = 'ชื่อผู้ใช้ไม่ถูกต้อง';
                    } else {
                        $error['pass'] = 'รหัสผ่านไม่ถูกต้อง';
                    }
                }

            } catch (Exception $e) {
                $error = $this->_getError($e->getMessage());
            }
        }

        if (!empty($error)) {
            $this->view->error = $error;
        }

        if(!empty($post) ){
            $this->view->post = $post;
        }

        $this->view->next = !empty($_REQUEST['next']) ? $_REQUEST['next'] : $_SERVER['REQUEST_URI'];
        $this->view->render("member/login");
    }

    public function social() {

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
}