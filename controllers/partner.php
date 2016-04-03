<?php

class Partner extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->title = "Partner - ". PAGE_TITLE;
        $this->view->theme = 'manage';
        $this->view->currentPage = 'partner';
        $this->view->elem('body')->addClass('hidden-tobar');
    }

    public function index() {
        
        $this->view->theme = 'default';
        $this->view->elem('body')->addClass('col-x');
        $this->view->render('partner/index');
    }

    public function register() {

        if( Cookie::get( COOKIE_KEY_PARTNER ) ){
            header('Location: ' . URL . 'partner/manage');
        }

        if (!empty($_POST)) {

            $dataPost = $_POST;
            try {
                $form = new Form();

                $form   ->post('partner_name')->val('is_empty')
                        ->post('partner_email')->val('is_empty')
                        ->post('partner_password')->val('password')->val('is_empty');

                $form->submit();
                $dataPost = $form->fetch();

                // ตรวจสอบอีเมล์
                if( filter_var($dataPost['partner_email'], FILTER_VALIDATE_EMAIL) ){

                    $ext = explode("@", $dataPost['partner_email']);

                    if( !in_array($ext[1], array('gmail.com','hotmail.com')) ){
                        $arr['error']['partner_email'] = "โปรดป้อนอีเมลที่ถูกต้อง!";
                    }else if ( $this->model->query('partner')->duplicate( $dataPost['partner_email'] ) ){
                        $arr['error']['partner_email'] = "อีเมลนี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                    }

                }elseif( is_numeric($dataPost['partner_email']) ){

                    if ( !@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}))?[0-9]{3}[0-9]{4}$", $dataPost['partner_email']) ){
                        $arr['error']['partner_email'] = "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 0843635952)";
                    }
                    else if ( $this->model->query('partner')->duplicate( $dataPost['partner_email'] ) ){
                        $arr['error']['partner_email'] = "หมายเลขโทรศัพท์นี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                    }else{
                        $dataPost['partner_tel'] = $dataPost['partner_email'];
                        unset($dataPost['partner_email']);
                    }

                }else{
                    $arr['error']['partner_email'] = "โปรดป้อนอีเมลที่ถูกต้อง";
                }

                if( empty($arr['error']) ){
                    $this->model->query('partner')->insert( $dataPost );

                    $arr['message'] = "ทะเบียนเรียบร้อยแล้ว";
                    $arr['url'] = URL . 'partner/login';
                    
                    // Cookie::set( COOKIE_KEY , $post['m_id'], time()+86400);
                    // header("location:".$arr['url']);
                }


            } catch (Exception $e) {
                $arr['error'] = $this->_getError( $e->getMessage() );
            }

            if( $this->format=='json' ){
                echo json_encode($arr);
                exit;
            }
            else if( !empty($arr['url']) ) {
                header('location:'. $arr['url']);
            }

        }

        if( !empty($arr['error']) ){
            // print_r($arr['error']); die;
            $this->view->error = $arr['error'];
        }

        if (!empty($dataPost)) {
            $this->view->post = $dataPost;
        }

        $this->view->css('login');
        $this->view->theme = 'login';
        $this->view->render('partner/Layouts/register');
    }

    public function regcomplete() {
        $this->view->currentPage = 'partner';
        $this->view->elem('body')->addClass('col-x');
        // $this->view->js('casino');
        $this->view->render('partner/regconplate');
    }

    public function login() {

        if( Cookie::get( COOKIE_KEY_PARTNER ) ){
            header('Location: ' . URL . 'partner/manage');
        }

        if (!empty($_POST)) {

            try {
                $form = new Form();

                $form   ->post('email')->val('is_empty')
                        ->post('pass')->val('is_empty');

                $form->submit();
                $post = $form->fetch();

                $login = $this->model->query('partner')->login($post['email'], $post['pass']);
                if (!empty($login)) {

                    if (Cookie::get(COOKIE_KEY_ADMIN)) {
                        Cookie::clear( COOKIE_KEY_ADMIN );
                    }

                    if(Cookie::get(COOKIE_KEY)){
                        Cookie::clear( COOKIE_KEY );
                    } 

                    Cookie::set(COOKIE_KEY_PARTNER, $login, time() + (86400 * 30)); // 30 วัน  
                    $arr['message'] = "เข้าสู่ระบบเรียบร้อยแล้ว";
                    $arr['url'] = URL . 'partner/member';
                } else {

                    if (!$this->model->query('partner')->duplicate($post['email'])) {
                        $arr['error']['email'] = 'ชื่อผู้ใช้ไม่ถูกต้อง';
                    } else {
                        $arr['error']['pass'] = 'รหัสผ่านไม่ถูกต้อง';
                    }

                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError( $e->getMessage() );
            }

            if( $this->format=='json' ){
                echo json_encode($arr);
                exit;
            }
            else if( !empty($arr['url']) ) {
                header('location:'. $arr['url']);
            }
        }

        $this->view->css('login');
        $this->view->theme = 'login';
        $this->view->render('partner/Layouts/login');
    }

    public function manage() {

        $this->member();
    }
    public function redirect($id) {
        Cookie::set('partner_redirect', $id, time() + (86400 * 1)); // 1 วัน   
        header('Location: ' . URL . 'register');
    }

    public function banner() {
        if( empty($this->me['partner_id']) ) $this->error();
        
        $this->view->currentPage = 'banner';
        $this->view->render('partner/banner');
    }
    // join member
    public function member( $section='', $id = null ) {
        if( empty($this->me['partner_id']) ) $this->error();
        $this->view->currentPage = 'member';

        if( $section=='add' ){

            if( !empty($_POST) ){
                try {
                    $form = new Form();
                    $form   ->post('m_name')->val('is_empty')
                            ->post('m_email')
                            ->post('m_phone_number')

                            ->post('m_username')->val('username')
                            ->post('m_password')->val('password')

                            ->post('m_note');

                    $form->submit();
                    $dataPost = $form->fetch();

                    // ตรวจสอบอีเมล์
                    if( !empty($dataPost['m_email']) ){

                        $err = $form->verify('email', $dataPost['m_email']);

                        if( !empty($err) ){
                            $arr['error']['m_email'] = $err;
                        } else if( $this->model->query('member')->is_user( $dataPost['m_email'] ) ){
                            $arr['error']['m_email'] = "ไม่สามารถใช้อีเมล์นี้ได้ (อีเมลนี้ถูกใช้ไปแล้ว)";
                        }
                    }

                    // ตรวจสอบg phone
                    if( !empty($dataPost['m_phone_number']) ){

                        $err = $form->verify('phone_number', $dataPost['m_phone_number']);

                        if( !empty($err) ){
                            $arr['error']['m_phone_number'] = $err;
                        } else if( $this->model->query('member')->is_user( $dataPost['m_phone_number'] ) ){
                            $arr['error']['m_phone_number'] = "ไม่สามารถใช้เบอร์โทรศัพท์นี้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
                        }
                    }

                    // ตรวจสอบชื่อผู้เข้าใช้
                    if( $this->model->query('member')->is_user( $dataPost['m_username'] ) ){
                        $arr['error']['m_username'] = "ไม่สามารถใช้ชื่อผู้เข้าใช้นี้ได้ (ชื่อผู้เข้าใช้นี้ถูกใช้ไปแล้ว)";
                    }
                    
                    if( empty($arr['error']) ){

                        $dataPost['m_partner_id'] = $this->me['partner_id'];
                        // insert 
                        $this->model->query('member')->insert( $dataPost );
                        $id = $dataPost['m_id'];

                        $this->model->query('partner')->joinMember($this->me['partner_id'], $id );

                        $arr['message'] = "เพิ่มสมาชิกเรียบร้อย";
                        $arr['url'] = URL.'partner/member/'.$id;
                    }

                } catch (Exception $e) {
                    $arr['error'] = $this->_getError($e->getMessage());
                }

                echo json_encode($arr);
            }
            else{
                $this->view->render('partner/member/dialog/add_form');
            }
            exit;
        } elseif ( $section=='edit' ) {

            $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
            $item = $this->model->query('member')->get( $id );
            if( empty($item)) $this->error();

            if( !empty($_POST) ){
                try {
                    $form = new Form();
                    $form   ->post('m_name')->val('maxlength', 20)->val('is_empty')
                            ->post('m_email')
                            ->post('m_phone_number')

                            ->post('m_username')->val('username')
                            ->post('m_note');

                    $form->submit();
                    $dataPost = $form->fetch();

                    // ตรวจสอบอีเมล์
                    if( !empty($dataPost['m_email']) ){

                        $err = $form->verify('email', $dataPost['m_email']);

                        if( !empty($err) ){
                            $arr['error']['m_email'] = $err;
                        } else if( $item['email']!=$dataPost['m_email'] && $this->model->query('member')->is_user( $dataPost['m_email'] ) ){
                            $arr['error']['m_email'] = "ไม่สามารถใช้อีเมล์นี้ได้ (อีเมลนี้ถูกใช้ไปแล้ว)";
                        }
                    }

                    // ตรวจสอบg phone
                    if( !empty($dataPost['m_phone_number']) ){

                        $err = $form->verify('phone_number', $dataPost['m_phone_number']);

                        if( !empty($err) ){
                            $arr['error']['m_phone_number'] = $err;
                        } else if( $item['phone_number']!=$dataPost['m_phone_number'] && $this->model->query('member')->is_user( $dataPost['m_phone_number'] ) ){
                            $arr['error']['m_phone_number'] = "ไม่สามารถใช้เบอร์โทรศัพท์นี้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
                        }
                    }

                    // ตรวจสอบชื่อผู้เข้าใช้
                    if( $item['username']!=$dataPost['m_username'] && $this->model->query('member')->is_user( $dataPost['m_username'] ) ){
                        $arr['error']['m_username'] = "ไม่สามารถใช้ชื่อผู้เข้าใช้นี้ได้ (ชื่อผู้เข้าใช้นี้ถูกใช้ไปแล้ว)";
                    }
                    
                    if( empty($arr['error']) ){

                        // update 
                        $this->model->query('member')->update($id, $dataPost);
                        $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                        $arr['url'] = URL.'partner/member/'.$id;
                    }

                } catch (Exception $e) {
                    $arr['error'] = $this->_getError($e->getMessage());
                }

                echo json_encode($arr);
            }
            else{
                $this->view->item = $item;
                $this->view->render('partner/member/dialog/edit_form');
            }

            exit;
        } elseif ( $section=='change_password' ) {

            $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
            $item = $this->model->query('member')->get( $id );
            if( empty($item)) $this->error();

            if( !empty($_POST) ){
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

                        $this->model->query('member')->update($item['m_id'], array( 'm_password' => $dataPost['password_new']) );

                        $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                        // $arr['url'] = 'refresh';
                    }


                } catch (Exception $e) {
                    $arr['error'] = $this->_getError($e->getMessage());
                }

                echo json_encode($arr);
            }
            else{
                $this->view->item = $item;
                $this->view->render('partner/member/dialog/change_password_form');
            }

            exit;
        } elseif ( $section=='del' ) {
            $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
            $item = $this->model->query('member')->get( $id );
            if( empty($item)) $this->error();

            if( !empty($_POST) ){
                $this->model->query('member')->delete( $id );
                
                // 
                if( !empty($item['partner_id']) ){
                    $this->model->query('partner')->delMember($item['partner_id'], $id);
                }

                $arr['message'] = "ลบเรียบร้อย";
                $arr['url'] = URL.'partner/member';
                echo json_encode($arr);
            }
            else{
                $this->view->item = $item;
                $this->view->render('partner/member/dialog/del_form');
            }
            exit;
        } elseif ( $section=='live_update' ) {

            $post['field']= isset($_REQUEST['field']) ? $_REQUEST['field']: null;
            $post['value'] = isset($_REQUEST['val']) ? $_REQUEST['val']: null;

            $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
            $item = $this->model->query('member')->get( $id );
            if( empty($item) || empty($post['field']) ) $this->error();

            $form = new Form();
            $arr['error_message'] = $form->check( array( 'email', 'phone_number', 'username', 'password'), $post['field'], $post['value'] );

            
            if( $post['field']=='email' ) {

                if( $this->model->query('member')->is_user($post['value']) && $post['value']!=$item['email'] ){
                    $arr['error_message'] = 'อีเมลนี้เคยลงทะเบียนไว้ก่อนแล้ว';
                }
            } else if( $post['field']=='phone_number' ) {
     
                if( $this->model->query('member')->is_user($post['value']) && $post['value']!=$item['phone_number'] ){
                    $arr['error_message'] = 'หมายเลขโทรศัพท์นี้เคยลงทะเบียนไว้ก่อนแล้ว';
                }

            } else if( $post['field']=='username' ) {
                if( $this->model->query('member')->is_user($post['value']) && $post['value']!=$item['user'] ){
                    $arr['error_message'] = 'ไม่สามารถใช้ชื่อผู้เข้าใช้นี้ได้ (ชื่อผู้เข้าใช้นี้เคยลงทะเบียนไว้ก่อนแล้ว)';
                }
            } else if( $post['field']=="password" && strlen($post['value'])<6 ){
                $arr['error_message'] = "รหัสผ่านต้องมีความยาว 6 ตัวขึ้นไป";
                
            } else if( $post['field']=='name' && $post['value']=="" ){
                $arr['error_message'] = "กรอกชื่อ-สกุล";
            }

            if( empty($arr['error_message']) ){

                // seve 
                $dataPost[ "m_".$post['field'] ] = $post['value'];

                $this->model->query('member')->update( $id , $dataPost);
            }

            $arr['error'] = !empty($arr['error_message']);

            echo json_encode( $arr );

            exit;
        } else if( is_numeric($section) ){
            $item = $this->model->query('member')->get( $section );
            if( empty($item)) $this->error();

            $this->view->results = $this->model->query('member')->playing( $item['m_id'] );

            if( $this->format=='json' ){

                $this->view->render('partner/member/profile/lists/json');
            }else{

                Session::init();                          
                Session::set('isPushedLeft', false);
                $this->view->elem('body')->addClass('is-overlay-left page-listpage');

                $this->view->item = $item;
                $this->view->render('partner/member/profile/display');
            }
            
            exit;
        }

        // 
        // echo 1; die;
        $this->view->results = $this->model->query('partner')->member( Cookie::get( COOKIE_KEY_PARTNER ) );
        if( $this->format=='json' ){
            $this->view->render('partner/member/lists/json');
        }
        else{
            $this->view->render('partner/member/lists/display');
        }
    }
    public function settings( $section = 'profile' ) {

        if( empty($this->me['partner_id']) || !in_array($section, array('profile', 'password')) ) $this->error();
        
        $this->view->currentPage = 'settings';
        $this->view->section = $section;
        $this->view->elem('body')->addClass('hidden-tobar settings-page');
        $this->view->render('partner/settings/display');
    }
    public function change_password() {

        if( empty($this->me['partner_id']) ) $this->error();

        $data = $_POST;

        if( $this->me['partner_password'] != $data['password_old'] ){
            $arr['error']['password_old'] = "รหัสของคุณไม่ถูกต้อง";
        }
        else if( $this->me['partner_password'] == $data['password_new'] ){
            $arr['error']['password_new'] = "รหัสใหม่จะเมือนกับรหัสเก่าไม่ได้";
        }
        else if( $data['password_confirm'] != $data['password_new'] ){
            $arr['error']['password_confirm'] = "คุณต้องใส่รหัสผ่านที่เหมือนกันสองครั้งเพื่อเป็นการยืนยัน";
        }
        else if( strlen($data['password_new']) < 4 ){
            $arr['error']['password_confirm'] = "ต้องมีความยาว 4 ตัวขึ้นไป";
        }

        if( empty($arr['error']) ){

            $this->model->query('partner')->update($this->me['partner_id'], array('partner_password' => $data['password_new'] ));
            $arr['url'] = 'refresh';
            $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
        }

        echo json_encode($arr);
    }


    // admin manage
    public function add() {

        $this->view->render('partner/dialog/add_or_edit_form');
    }
    public function edit($id = null) {
        if (empty($id))
            $this->_error();

        $item = $this->model->query('partner')->get($id);
        if (empty($item))
            $this->error();

        $this->view->item = $item;
        $this->view->render('partner/dialog/add_or_edit_form');
    }
    public function update($id = null) {

        if (empty($_POST))
            $this->_error();

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (!empty($id)) {
            $item = $this->model->query('partner')->get($id);
            if (empty($item))
                $this->error();
        }

        try {
            $form = new Form();
            $form   ->post('partner_email')->val('email')
                    ->post('partner_name')
                    ->post('partner_tel')->val('phone_number');

            $form->submit();
            $data = $form->fetch();

            // ตรวจสอบ email ซ้ำ
            if (!empty($item)) {
                if ($item['partner_email'] != $data['partner_email'] && $this->model->query('partner')->duplicate($data['partner_email']))
                    $arr['error']['partner_email'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
            }
            else if ($this->model->query('partner')->duplicate($data['partner_email'])) {
                $arr['error']['partner_email'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
            }

            // ตรวจสอบเบอร์โทร ซ้ำ
            if (!empty($item)) {
                if ($item['partner_tel'] != $data['partner_tel'] && $this->model->query('partner')->duplicate($data['partner_tel']))
                    $arr['error']['partner_tel'] = "ไม่สามารถใช้เบอร์โทรศัพท์นี้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
            }
            else if ($this->model->query('partner')->duplicate($data['partner_tel'])) {
                $arr['error']['partner_tel'] = "ไม่สามารถใช้เบอร์โทรศัพท์นี้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
            }

            if (isset($_POST['partner_password'])) {
                if (strlen($_POST['partner_password']) < 4) {
                    $arr['error']['partner_tel'] = "รหัสผ่านต้องมี 4 ตัวขึ้นไป";
                } else {
                    $data['partner_password'] = $_POST['partner_password'];
                }
            }

            if (empty($arr['error'])) {


                if (!empty($item)) {
                    // edit
                    $this->model->query('partner')->update($id, $data);
                    $arr['message'] = "แก้ไขข้อมูล partner เรียบร้อย";
                } else {

                    // insert 
                    $this->model->query('partner')->insert($data);
                    $id = $data['partner_id'];
                    $arr['message'] = "เพิ่ม partner เรียบร้อย";
                }

                $arr['url'] = !empty($_REQUEST['next']) ? $_REQUEST['next'] : 'refresh';
            }
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }
    public function change_pass($id = null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if ($this->format != 'json' || empty($id))
            $this->error();

        $item = $this->model->query('partner')->get($id);
        if (empty($item))
            $this->error();

        // 
        if (!empty($_POST)) {
            try {
                $form = new Form();
                $form->post('password_new')->val('password', 4)
                        ->post('password_confirm');

                $form->submit();
                $dataPost = $form->fetch();

                if ($dataPost['password_new'] != $dataPost['password_confirm']) {
                    $arr['error']['password_confirm'] = 'รหัสผ่านไม่ตรงกัน';
                }

                if (empty($arr['error'])) {

                    // update
                    $this->model->query('partner')->update($id, array('partner_password' => $dataPost['password_new']));

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                    // $arr['url'] = 'refresh';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        } else {
            $this->view->item = $item;
            $this->view->render('partner/dialog/change_pass_form');
        }
    }
    public function del($id = null) {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if ($this->format != 'json' || empty($id))
            $this->error();

        $item = $this->model->query('partner')->get($id);
        if (empty($item))
            $this->error();


        if (!empty($_POST)) {

            $this->model->query('partner')->delete($id);
            $arr['message'] = "ลบเรียบร้อย";
            $arr['url'] = URL . "manage/partner";
            echo json_encode($arr);
        } else {

            $this->view->item = $item;
            $this->view->render('partner/dialog/del_form');
        }
    }

    /**/
    /* live */

    public function live_update($id = null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        $data['field'] = isset($_REQUEST['field']) ? $_REQUEST['field'] : null;
        $data['value'] = isset($_REQUEST['val']) ? $_REQUEST['val'] : null;
        if ($this->format != 'json' || empty($id) || empty($data['field']))
            $this->error();

        $item = $this->model->query('partner')->get($id);
        if (empty($item))
            $this->error();

        $form = new Form();

        // $arr['error_message'] = $form->check( array('partner_email', 'partner_name', 'partner_tel', 'partner_note'), $data['field'], $data['value'] );


        if ($data['field'] == 'partner_email') {

            $arr['error_message'] = $form->verify('email', $data['value']);

            if (empty($arr['error_message'])) {

                if ($item['partner_email'] != $data['partner_email'] && $this->model->query('partner')->duplicate($data['partner_email'])) {

                    $arr['error_message'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
                }
            }
        } else if ($data['field'] == 'partner_tel') {

            $arr['error_message'] = $form->verify('phone_number', $data['value']);
            if (empty($arr['error_message'])) {

                if ($item['partner_tel'] != $data['value'] && $this->model->query('partner')->duplicate($data['value'])) {
                    $arr['error_message'] = "เบอร์โทรศัพท์ม่สามารถใช้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
                }
            }
        }

        if (empty($arr['error_message'])) {
            // seve 

            $post[$data['field']] = $data['value'];
            $this->model->query('partner')->update($id, $post);
        }

        $arr['error'] = !empty($arr['error_message']);

        echo json_encode($arr);
    }

}
