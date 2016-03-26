<?php

class Users extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function index(){
    	$this->error();
    }

    public function add($access_id=null){
    	if( empty($this->me) || $this->format!='json' ) $this->error();

        if( !empty($_POST) ){
            try {
                $form = new Form();
                $form   ->post('name')->val('name')->val('maxlength', 40)->val('is_empty')
                        ->post('access_id')->val('is_empty')
                        ->post('email')->val('is_empty')
                        ->post('pass')->val('password');

                $form->submit();
                $dataPost = $form->fetch();

                // ตรวจสอบอีเมล์
                if( filter_var($dataPost['email'], FILTER_VALIDATE_EMAIL) ){

                    $ext = explode("@", $dataPost['email']);

                    if( !in_array($ext[1], array('gmail.com','hotmail.com')) ){
                        $arr['error']['email'] = "โปรดป้อนอีเมลที่ถูกต้อง!";
                    }else if ( $this->model->query('users')->is_user( $dataPost['email'] ) ){
                        $arr['error']['email'] = "อีเมลนี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                    }

                }elseif( is_numeric($dataPost['email']) ){

                    if ( !@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}))?[0-9]{3}[0-9]{4}$", $dataPost['email']) ){
                        $arr['error']['email'] = "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 0843635952)";
                    }
                    else if ( $this->model->query('users')->is_user( $dataPost['email'] ) ){
                        $arr['error']['email'] = "หมายเลขโทรศัพท์นี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                    }else{
                        $dataPost['phone_number'] = $dataPost['email'];
                        unset($dataPost['email']);
                    }

                }else{
                    $arr['error']['email'] = "โปรดป้อนอีเมลที่ถูกต้อง";
                }

                if( empty($arr['error']) ){

                    $dataPost['display'] = 'enabled';
                    $this->model->query('users')->insert($dataPost);

                    $arr['message'] = "เพิ่มข้อมูลเรียบร้อย";
                    $arr['url'] = 'refresh';
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        }
        else{
            $this->view->access_id = isset($_REQUEST['access_id']) ? $_REQUEST['access_id']: $access_id;            
            $this->view->render('users/dialog/form_add');
        }
    }

    public function edit($id=null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('users')->get($id);
        if( empty($item) )$this->error();

        if( !empty($_POST) ){
            try {
                $form = new Form();
                $form   ->post('name')->val('name')->val('maxlength', 40)->val('is_empty')
                        ->post('email')
                        ->post('phone_number');

                $form->submit();
                $dataPost = $form->fetch();

                if( empty($dataPost['email']) && empty($dataPost['phone_number']) ){
                    $arr['error']['email'] = 'จำเป็นต้องกรอกอีเมลล์';
                }
                else{

                    if( !empty($dataPost['email']) ){

                        $ext = explode("@", $dataPost['email']);
                        if (!filter_var($dataPost['email'], FILTER_VALIDATE_EMAIL)){
                            $arr['error']['email'] = "นั่นไม่ใช่อีเมลล์ที่ถูกต้อง";
                        }
                        else if( !in_array($ext[1], array('gmail.com','hotmail.com')) ){
                            $arr['error']['email'] = "ไม่สามารถใช่อีเมลล์ @ นี้ได้! ระบบเรารองรับ @gmail.com และ @hotmail.com เท่านั้น";
                        }
                        else if ( $dataPost['email']!=$item['email'] && $this->model->query('users')->is_user( $dataPost['email'] ) ){
                            $arr['error']['email'] = "อีเมลนี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                        }

                    }

                    if( !empty($dataPost['phone_number']) ){

                        if ( !@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}))?[0-9]{3}[0-9]{4}$",$dataPost['phone_number']) ){
                            $arr['error']['phone_number'] = "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 084-363-5952)";
                        }
                        else if (  $dataPost['phone_number']!=$item['phone_number'] && $this->model->query('users')->is_user( $dataPost['phone_number'] ) ){
                            $arr['error']['phone_number'] = "หมายเลขโทรศัพท์นี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                        }
                    }
                }

                if( empty($arr['error']) ){

                    $this->model->query('users')->update($id, $dataPost);

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                    $arr['url'] = 'refresh';
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        }
        else{
            $this->view->item = $item;
            $this->view->render('users/dialog/form_edit');
        }
    }

    public function change_pass($id=null) {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('users')->get($id);
        if( empty($item) )$this->error();

        if( !empty($_POST) ){
            try {
                $form = new Form();
                $form   ->post('password_new')->val('password')
                        ->post('password_confirm')->val('password');

                $form->submit();
                $dataPost = $form->fetch();

                if( $dataPost['password_new']!=$dataPost['password_confirm'] ){
                    $arr['error']['password_confirm'] = 'รหัสผ่านไม่ตรงกัน';
                }

                if( empty($arr['error']) ){

                    // update
                    $this->model->query('users')->update($item['user_id'], array(
                        'pass' => Hash::create('sha256', $dataPost['password_new'], HASH_PASSWORD_KEY )
                    ));

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                    $arr['url'] = 'refresh';
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        }
        else{
            $this->view->item = $item;
            $this->view->render('users/dialog/form_change_pass');
        }
    }

    public function del($id=null){
       $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('users')->get($id);
        if( empty($item) )$this->error();

        if( !empty($_POST) ){
                
            $this->model->query('users')->delete($item['user_id']);
            $arr['message'] = "ลบข้อมูลเรียบร้อย";
            $arr['url'] = 'refresh';
            echo json_encode($arr);
        }
        else{
            $this->view->item = $item;
            $this->view->render('users/dialog/form_del');
        }
    }
}