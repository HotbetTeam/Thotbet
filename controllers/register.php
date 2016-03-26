<?php

class Register extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    function index() {

        // print_r($_POST); die;
        if( !empty($this->me) ){
            header("location:".URL);
        }

    	if( !empty($_POST) ){

            $dataPost = $_POST;
            try {
                $form = new Form();

                $form   ->post('name')->val('is_empty')
                        ->post('email')->val('is_empty')
                        ->post('password')->val('password')->val('is_empty');

                $form->submit();
                $dataPost = $form->fetch();

                // ตรวจสอบอีเมล์
                if( filter_var($dataPost['email'], FILTER_VALIDATE_EMAIL) ){

                    $ext = explode("@", $dataPost['email']);

                    if( !in_array($ext[1], array('gmail.com','hotmail.com')) ){
                        $arr['error']['email'] = "โปรดป้อนอีเมลที่ถูกต้อง!";
                    }else if ( $this->model->query('member')->is_user( $dataPost['email'] ) ){
                        $arr['error']['email'] = "อีเมลนี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                    }

                }elseif( is_numeric($dataPost['email']) ){

                    if ( !@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}))?[0-9]{3}[0-9]{4}$", $dataPost['email']) ){
                        $arr['error']['email'] = "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 0843635952)";
                    }
                    else if ( $this->model->query('member')->is_user( $dataPost['email'] ) ){
                        $arr['error']['email'] = "หมายเลขโทรศัพท์นี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                    }else{
                        $dataPost['phone_number'] = $dataPost['email'];
                        unset($dataPost['email']);
                    }

                }else{
                    $arr['error']['email'] = "โปรดป้อนอีเมลที่ถูกต้อง";
                }

                if( empty($arr['error']) ){

                    // $dataPost['access_id'] = 2;
                    $post['m_status'] = 'verify';
                    foreach ($dataPost as $key => $value) {
                        $post['m_'.$key] = $value;
                    }
                    // print_r($post); die;

                    $this->model->query('member')->insert( $post );

                    $arr['message'] = "ยินดีต้อนรับคุณ {$dataPost['name']}";
                    $arr['url'] = URL;

                    // Confirm Email
                    /*$code = Hash::create('sha256', rand(10,100), HASH_GENERAL_KEY);
                    $options = array(
                        'url'=>URL."accounts/confrim?new=1&code={$code}&uid={$dataPost['user_id']}&utm_campaign=emailconfirmwlc",
                        'title'=> "ยืนยันอีเมลของคุณ",
                        'name'=> "{$arr['data']['name']} {$arr['data']['name']}",
                        'email' => $arr['data']['email']
                    );
                    $mail = new Mailer();
                    $mail->confirmEmail( $options );*/

                    Cookie::set( COOKIE_KEY , $post['m_id'], time()+86400);
                    header("location:".$arr['url']);
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError( $e->getMessage() );
            }
        }

        if( !empty($arr['error']) ){
        	$this->view->error = $arr['error'];
        }

        if (!empty($dataPost)) {
            $this->view->post = $dataPost;
        }

        $this->view->elem('body')->addClass('register_page loggedOut ncx');
        $this->view->render('member/register');
    	
    }

    /*public function confirm_phone_number()
    {
        // if( empty($this->me) ) $this->error();
        
        $this->view->render('users/confirm_phone_number');
    }*/

}