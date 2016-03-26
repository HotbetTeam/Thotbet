<?php

class Member extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->formatPage = 'manage';
    }
    
    public function index($id=null) {
    	$this->view->currentPage = "member";

    	if( !empty($id) ){
    		$item = $this->model->query('member')->get($id);
    		if( empty($item) ) $this->error();

    		$nav[] = array( 'text' => 'สมาชิก', 'url' => URL.'member' );
    		$nav[] = array( 'text' => $item['name'] );
    		$this->view->nav = $nav;

            $this->view->results = $this->model->query('member')->playing( $item['m_id'] );
            $this->view->item = $item;
    		// print_r($item); die;

            if( $this->format=='json' ){
                $this->view->render('member/profile/lists/json');
            }
            else{

                Session::init();                          
                Session::set('isPushedLeft', false);
                $this->view->level = $this->model->query('member')->levelLists();
                $this->view->elem('body')->addClass('is-overlay-left page-listpage');
                $this->view->render('member/profile/display');
            }
    	}
    	else{

            $status = isset($_REQUEST['status']) ? $_REQUEST['status']: null;
            $this->view->results = $this->model->query('member')->lists();
            $this->view->status = $status;

            if( $this->format=='json' ){
                // sleep(1);
                $this->view->render('member/lists/json');
            }
            else{

                $this->view->elem('body')->addClass('hidden-tobar');
                
                $this->view->statusCounts = $this->model->query('member')->statusCounts();
            	$this->view->render('member/lists/display');
            }
    	}  
    }

    /**/
    /* new */
    public function add(){
    	if( empty($this->me) || $this->format!='json' ) $this->error();

    	if( !empty($_POST) ){
    		try {
	            $form = new Form();
	            $form   ->post('m_name')->val('is_empty')
                        ->post('m_email')
                        ->post('m_phone_number')

                        ->post('m_username')->val('username')
                        ->post('m_password')->val('password')

                        ->post('m_level_id')
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

		        	$dataPost['m_status'] = 'play';
		        	// insert 
		        	$this->model->query('member')->insert( $dataPost );
		        	$id = $dataPost['m_id'];

		        	$arr['message'] = "เพิ่มสมาชิกเรียบร้อย";
		        	$arr['url'] = URL.'member/'.$id;
		        }

	        } catch (Exception $e) {
	            $arr['error'] = $this->_getError($e->getMessage());
	        }

	        echo json_encode($arr);
    	}
    	else{

            $this->view->level = $this->model->query('member')->levelLists();
    		$this->view->render('member/dialog/form_add');
    	}
    }

    /**/
    /* update */
    public function edit($id=null){
    	$id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

    	if( !empty($id) ){
    		$item = $this->model->query('member')->get($id);
    		if(empty($item)) $this->error();
    	}

    	// 
    	if( !empty($_POST) ){
    		try {
	            $form = new Form();
                $form   ->post('m_name')->val('name')->val('maxlength', 20)->val('is_empty')
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
		        	$this->model->query('member')->update($item['m_id'], $dataPost);
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
    		$this->view->render('member/dialog/form_edit');
    	}
    }
    public function change_pass($id=null){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        if( !empty($id) ){
            $item = $this->model->query('member')->get($id);
            if(empty($item)) $this->error();
        }

        // 
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

                    // update$pess, HASH_PASSWORD_KEY
                    $this->model->query('member')->update($item['m_id'], array( 'm_password' => $dataPost['password_new']) );

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                    $arr['url'] = 'refresh';
                }


            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);

        } else{
            $this->view->item = $item;
            $this->view->render('member/dialog/form_change_pass');
        }
    }
    public function change_level($id=null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('member')->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){
            
            $this->model->query('member')->update( $item['m_id'], array('m_level_id' => $_POST['level_id']) );
            $arr['message'] = "เปลี่ยนระดับสมาชิกเรียบร้อย";
            $arr['url'] = "refresh";
            echo json_encode($arr);
        }
        else{
            
            $this->view->level = $this->model->query('member')->levelLists();
            $this->view->item = $item;
            $this->view->render('member/dialog/form_change_level');
        }
    }
    public function change_status($id=null,$status=null) {
    	$id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
    	$status = isset($_REQUEST['status']) ? $_REQUEST['status']: $status;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('member')->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){

            $this->model->query('member')->update($item['m_id'], array(
        		'm_status' => $status
        	));
            $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
		    $arr['url'] = 'refresh';
            echo json_encode($arr);
        }
        else{
        	$this->view->status = $status;
            $this->view->item = $item;
            $this->view->render('member/dialog/form_change_status');
        }
    }
    public function del( $id=null) {

    	$id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('member')->get($id);
    	if( empty($item) ) $this->error();

        if( !empty($_POST) ){
            
            $this->model->query('member')->delete( $item['m_id'] );
            $arr['message'] = "ลบเรียบร้อย";
            $arr['url'] = "refresh";
            echo json_encode($arr);
        }
        else{
           
            $this->view->item = $item;
            $this->view->render('member/dialog/form_del');
        }
    }
    public function confrim($id=null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('member')->get($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){

            try {
                $form = new Form();
                $form   ->post('game_user')->val('username')
                        ->post('game_pass')->val('password', 4);

                $form->submit();
                $dataPost = $form->fetch();

                if( $dataPost['game_user']!=$item['game_user'] && $this->model->query('member')->is_game_user( $dataPost['game_user'] ) ){
                    $arr['error']['game_user'] = 'ชื่อผู้เข้าใช้นี้มีอยู่แล้ว!';
                }
                else{

                    $dataPost['m_status'] = 'play';
                    $this->model->query('member')->update( $item['m_id'], $dataPost );
                    $arr['message'] = "อนุมัติสมาชิกเรียบร้อย";
                    $arr['url'] = "refresh";
                }

            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        }
        else{
           
            $this->view->item = $item;
            $this->view->render('member/dialog/form_confrim');
        }
    }
    public function change_about_game($id=null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        if( !empty($id) ){
            $item = $this->model->query('member')->get($id);
            if(empty($item)) $this->error();
        }

        if( !empty($_POST) ){
            try {
                $form = new Form();
                $form   ->post('game_user')->val('username')
                        ->post('game_pass')->val('password', 4);

                $form->submit();
                $dataPost = $form->fetch();

                if( $dataPost['game_user']!=$item['game_user'] && $this->model->query('member')->is_game_user( $dataPost['game_user'] ) ){
                    $arr['error']['game_user'] = 'ชื่อผู้เข้าใช้นี้มีอยู่แล้ว!';
                }
                else{

                    // update$pess, HASH_PASSWORD_KEY
                    $this->model->query('member')->update($item['m_id'], $dataPost);

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                    $arr['url'] = 'refresh';
                }


            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);

        } else{
            $this->view->item = $item;
            $this->view->render('member/dialog/form_change_about_game');
        }
    }
    

    /**/
    /* point */
    public function change_point(){

        if( empty($this->me) || $this->format!='json' || empty($_POST) ) $this->error();

        try {
            $form = new Form();
            $form   ->post('menber')->val('numeric')->val('space')
                    ->post('actual')->val('numeric')->val('space');

            $form->submit();
            $post = $form->fetch();


            $post['menber'] /= 100;
            $post['actual'] /= 100;

            $this->model->query('member')->setPoint( $post );
            $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
            $arr['url'] = "refresh";
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }


    /**/
    /* level */
    public function form_level($id=null) {

        if( empty($this->me) || $this->format!='json' ) $this->error();

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( !empty($id) ){
            $item = $this->model->query('member')->getLevel($id);

            if( empty($item) ) $this->error();
            $this->view->item = $item;
        }

        $this->view->render('member/dialog/level_form');
    }
    public function save_level($id=null){

        if( empty($this->me) || $this->format!='json' || empty($_POST) ) $this->error();
        
        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( !empty($id) ){
            $item = $this->model->query('member')->getLevel($id);
            if( empty($item) ) $this->error();
        }

        try {
            $form = new Form();
            $form   ->post('lev_name')->val('is_empty')
                    ->post('lev_score')->val('numeric')->val('space');

            $form->submit();
            $post = $form->fetch();

            $post['lev_has_auto'] = isset( $_POST['lev_has_auto']) ? $_POST['lev_has_auto']:false;

            if( !empty($item) ){
                $this->model->query('member')->editLevel($id, $post );
            }
            else{
                $this->model->query('member')->setLevel( $post );
            }

            $arr['message'] = 'บันทึกข้อมูลเรียบร้อย';
            $arr['url'] = 'refresh';

        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }
    public function form_level_del($id=null) {

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('member')->getLevel($id);
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){
            
            $this->model->query('member')->delLevel( $id );
            $arr['message'] = 'ลบข้อมูลเรียบร้อย';
            $arr['url'] = "refresh";
            echo json_encode($arr);
        }
        else{

            $this->view->item = $item;
            $this->view->render('member/dialog/level_del_confirm');
        }
    }

    /**/
    /* live */
    public function live_update($id=null){

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        $post['field']= isset($_REQUEST['field']) ? $_REQUEST['field']: null;
        $post['value'] = isset($_REQUEST['val']) ? $_REQUEST['val']: null;
        if( empty($this->me) || $this->format!='json' || empty($id) || empty($post['field']) ) $this->error();

        $item = $this->model->query('member')->get( $id );
        if( empty($item) ) $this->error();

        $form = new Form();
       
        $arr['error_message'] = $form->check( array('name', 'email', 'phone_number', 'username', 'password'), $post['field'], $post['value'] );

        
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
            
        } else if( $post['value']=="" ){

        }

        if( empty($arr['error_message']) ){

            // seve 
            $dataPost[ "m_".$post['field'] ] = $post['value'];
            $this->model->query('member')->update( $id , $dataPost);
        }

        $arr['error'] = !empty($arr['error_message']);



        echo json_encode( $arr );
    }

}