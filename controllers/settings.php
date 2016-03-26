<?php

class Settings extends Controller {

    function __construct() {
        parent::__construct();
        
    }
    
    public function index() {

        if( empty($this->me) ) $this->error();


        $this->view->render('member/settings/display');
    }

    public function basic() {
        if( empty($this->me) ) $this->error();

        if( !empty($_POST) ){

            $dataPost = array();
            $arr = array();
            try {
                $form = new Form();
                $form   ->post('name')->val('maxlength', 20)->val('is_empty')
                        ->post('email')

                        // ->post('username')->val('username')
                        ->post('phone_number');

                $form->submit();
                $dataPost = $form->fetch();

                // ตรวจสอบอีเมล์
                if( !empty($dataPost['email']) ){

                    $err = $form->verify('email', $dataPost['email']);

                    if( !empty($err) ){
                        $arr['error']['email'] = $err;
                    } else if( $this->me['email']!=$dataPost['email'] && $this->model->query('member')->is_user( $dataPost['email'] ) ){
                        $arr['error']['email'] = "ไม่สามารถใช้อีเมล์นี้ได้ (อีเมลนี้ถูกใช้ไปแล้ว)";
                    }
                }

                // ตรวจสอบg phone
                if( !empty($dataPost['phone_number']) ){

                    $err = $form->verify('phone_number', $dataPost['phone_number']);

                    if( !empty($err) ){
                        $arr['error']['phone_number'] = $err;
                    } else if( $this->me['phone_number']!=$dataPost['phone_number'] && $this->model->query('member')->is_user( $dataPost['phone_number'] ) ){
                        $arr['error']['phone_number'] = "ไม่สามารถใช้เบอร์โทรศัพท์นี้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
                    }
                }

                // ตรวจสอบชื่อผู้เข้าใช้
                /*if( $this->me['username']!=$dataPost['username'] && $this->model->query('member')->is_user( $dataPost['username'] ) ){
                    $arr['error']['username'] = "ไม่สามารถใช้ชื่อผู้เข้าใช้นี้ได้ (ชื่อผู้เข้าใช้นี้ถูกใช้ไปแล้ว)";
                }*/
                
                if( empty($arr['error']) ){

                    foreach ($dataPost as $key => $value) {
                        $post['m_'.$key] = $value;
                    }
             
                    // update 
                    $this->model->query('member')->update($this->me['m_id'], $post);
                    $this->view->message = 'แก้ไขข้อมูลเรียบร้อย';
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            $post = array_merge($this->me, $dataPost);
            $post = array_merge($post, $arr);

            $this->view->me = $post;
            // print_r($post); die;
        }

        $this->view->render('member/settings/display');
    }

    public function password() {
        if( empty($this->me) ) $this->error();

        if( !empty($_POST) ){

            $arr = array();

            try {
                $form = new Form();
                $form   ->post('password_new')->val('password')
                        ->post('password_confirm');

                $form->submit();
                $dataPost = $form->fetch();

                if( $dataPost['password_new']!=$dataPost['password_confirm'] ){
                    $arr['error']['password_confirm'] = 'รหัสผ่านไม่ตรงกัน';
                }

                if( empty($arr['error']) ){

                    // update$pess, HASH_PASSWORD_KEY
                    $this->model->query('member')->update($this->me['m_id'], array( 'm_password' => $dataPost['password_new']) );

                    $data['password']['message'] = 'แก้ไขข้อมูลเรียบร้อย';
                    $this->view->post = $data;
                }


            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            if( !empty($arr['error']) ){

                $data['password']['error'] = $arr['error'];
                $this->view->post = $data;
            }

        }


        $this->view->render('member/settings/display');
    }

    public function change_phone_number(){
        if( empty($this->me) || $this->format!='json' || empty($_POST) ) $this->error();

        try {
            $form = new Form();
            $form   ->post('phone_number')->val('phone_number');

            $form->submit();
            $dataPost = $form->fetch();

            if( $this->me['phone_number']!=$dataPost['phone_number'] && $this->model->query('member')->is_user( $dataPost['phone_number'] ) ){
                $arr['error']['phone_number'] = "ไม่สามารถใช้เบอร์โทรศัพท์นี้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
            }

            if( empty($arr['error']) ){

                $this->model->query('member')->update($this->me['m_id'], array( 'm_phone_number' => $dataPost['phone_number']) );
                $arr['message'] = 'เพิ่มเบอร์โทรของคุณเรียบร้อย';
                $arr['url'] = 'refresh';
            }


        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }
    

}