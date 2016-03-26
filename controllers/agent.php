<?php

class Agent extends Controller {

    function __construct() {
        parent::__construct();
 
        $this->view->theme = 'manage';
 
    }

    public function index() {
        if (!empty($_COOKIE[COOKIE_KEY_AGENT])) {
            header('Location: ' . URL . 'agent/manage');
        }
        $this->view->currentPage = 'agent';

        $this->view->elem('body')->addClass('col-x');
        // $this->view->js('casino');
        $this->view->render('agent/index');
    }

    public function register() {
        if (!empty($_COOKIE[COOKIE_KEY_AGENT])) {
            header('Location: ' . URL . 'agent/manage');
        }
        if (!empty($_POST)) {
            $dataPost = $_POST;
            if (filter_var($dataPost['agent_email'], FILTER_VALIDATE_EMAIL)) {

                $ext = explode("@", $dataPost['agent_email']);
                if ($this->model->query('agent')->duplicate($dataPost['agent_email'])) {
                    $arr['error']['agent_email'] = "อีเมลนี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                }
            } elseif (is_numeric($dataPost['email'])) {

                if (!@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}))?[0-9]{3}[0-9]{4}$", $dataPost['agent_email'])) {
                    $arr['error']['agent_email'] = "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 0843635952)";
                } else if ($this->model->query('agent')->duplicate($dataPost['agent_email'])) {
                    $arr['error']['agent_email'] = "หมายเลขโทรศัพท์นี้เคยลงทะเบียนไว้ก่อนแล้ว!";
                }
            } else {
                $arr['error']['agent_email'] = "โปรดป้อนอีเมลที่ถูกต้อง";
            }
            if (empty($arr['error'])) {
                $dataPost['agent_regisdate'] = date('Y-m-d');
                $this->model->db->insert('agent', $dataPost);

                $arr['message'] = "ยินดีต้อนรับคุณ {$dataPost['agent_name']}";
                $arr['url'] = URL . 'agent/regcomplete';
                header("location:" . $arr['url']);
            } else {
                $this->view->error = $arr['error'];
            }
        }
        if (!empty($dataPost)) {
            $this->view->post = $dataPost;
        }


        $this->view->currentPage = 'agent';
        $this->view->elem('body')->addClass('col-x');
        // $this->view->js('casino');
        $this->view->render('agent/register');
    }

    public function regcomplete() {
        $this->view->currentPage = 'agent';
        $this->view->elem('body')->addClass('col-x');
        // $this->view->js('casino');
        $this->view->render('agent/regconplate');
    }

    public function login() {
        if (!empty($_POST)) {
            $email = $_POST['agent_email'];
            $password = $_POST['agent_password'];
            $login = $this->model->query('agent')->login($email, $password);
            if (!empty($login)) {
                Cookie::set(COOKIE_KEY_AGENT, $login, time() + (86400 * 30)); // 30 วัน            
                header('Location: ' . URL . 'agent/manage/');
            } else {
                $arr['error']['error'] = "อีเมล หรือ รหัสผ่านไม่ถูกต้อง";
                $this->view->error = $arr['error'];
            }
        }
        $this->view->currentPage = 'agent';
        $this->view->elem('body')->addClass('col-x');
        // $this->view->js('casino');
        $this->view->render('agent/login');
    }

    public function manage() {
        $this->view->currentPage = 'agent';
        $this->view->elem('body')->addClass('col-x');
        $this->view->render('agent/manage');
    }

    public function cbanners() {
        $this->view->currentPage = 'agent';
        $this->view->elem('body')->addClass('col-x');
        $this->view->render('agent/banner');
    }

    public function redirect($id) {
        Cookie::set('Agentredirect', $id, time() + (86400 * 30)); // 30 วัน   
        header('Location: ' . URL . 'register');
    }

    public function user() {
        $this->view->results = $this->model->query('agent')->member($_COOKIE[COOKIE_KEY_AGENT]);

        if( $this->format=='json' ){
            $this->view->render('agent/lists/json');
        }
        else{
            $this->view->render('genat/lists/display');
        }

    }

    // admin manage
    public function add() {

        $this->view->render('agent/dialog/add_or_edit_form');
    }

    public function edit($id = null) {
        if (empty($id))
            $this->_error();

        $item = $this->model->query('agent')->get($id);
        if (empty($item))
            $this->error();

        $this->view->item = $item;
        $this->view->render('agent/dialog/add_or_edit_form');
    }

    public function update($id = null) {

        if (empty($_POST))
            $this->_error();

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if (!empty($id)) {
            $item = $this->model->query('agent')->get($id);
            if (empty($item))
                $this->error();
        }

        try {
            $form = new Form();
            $form->post('agent_email')->val('email')
                    ->post('agent_name')
                    ->post('agent_tel')->val('phone_number');

            $form->submit();
            $data = $form->fetch();

            // ตรวจสอบ email ซ้ำ
            if (!empty($item)) {
                if ($item['agent_email'] != $data['agent_email'] && $this->model->query('agent')->duplicate($data['agent_email']))
                    $arr['error']['agent_email'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
            }
            else if ($this->model->query('agent')->duplicate($data['agent_email'])) {
                $arr['error']['agent_email'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
            }

            // ตรวจสอบเบอร์โทร ซ้ำ
            if (!empty($item)) {
                if ($item['agent_tel'] != $data['agent_tel'] && $this->model->query('agent')->duplicate($data['agent_tel']))
                    $arr['error']['agent_tel'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
            }
            else if ($this->model->query('agent')->duplicate($data['agent_tel'])) {
                $arr['error']['agent_tel'] = "เบอร์โทรศัพท์ม่สามารถใช้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
            }


            if (isset($_POST['agent_password'])) {
                if (strlen($_POST['agent_password']) < 4) {
                    $arr['error']['agent_tel'] = "รหัสผ่านต้องมี 4 ตัวขึ้นไป";
                } else {
                    $data['agent_password'] = $_POST['agent_password'];
                }
            }

            if (empty($arr['error'])) {


                if (!empty($item)) {
                    // edit
                    $this->model->query('agent')->update($id, $data);
                    $arr['message'] = "แก้ไขข้อมูล Agent เรียบร้อย";
                } else {

                    // insert 
                    $this->model->query('agent')->insert($data);
                    $id = $data['agent_id'];
                    $arr['message'] = "เพิ่ม Agent เรียบร้อย";
                }

                $arr['url'] = URL . 'manage/agent/' . $id;
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

        $item = $this->model->query('agent')->get($id);
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
                    $this->model->query('agent')->update($id, array('agent_password' => $dataPost['password_new']));

                    $arr['message'] = "แก้ไขข้อมูลเรียบร้อย";
                    // $arr['url'] = 'refresh';
                }
            } catch (Exception $e) {
                $arr['error'] = $this->_getError($e->getMessage());
            }

            echo json_encode($arr);
        } else {
            $this->view->item = $item;
            $this->view->render('agent/dialog/change_pass_form');
        }
    }

    public function del($id = null) {
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if ($this->format != 'json' || empty($id))
            $this->error();

        $item = $this->model->query('agent')->get($id);
        if (empty($item))
            $this->error();


        if (!empty($_POST)) {

            $this->model->query('agent')->delete($id);
            $arr['message'] = "ลบเรียบร้อย";
            $arr['url'] = URL . "manage/agent";
            echo json_encode($arr);
        } else {

            $this->view->item = $item;
            $this->view->render('agent/dialog/del_form');
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

        $item = $this->model->query('agent')->get($id);
        if (empty($item))
            $this->error();

        $form = new Form();

        // $arr['error_message'] = $form->check( array('agent_email', 'agent_name', 'agent_tel', 'agent_note'), $data['field'], $data['value'] );


        if ($data['field'] == 'agent_email') {

            $arr['error_message'] = $form->verify('email', $data['value']);

            if (empty($arr['error_message'])) {

                if ($item['agent_email'] != $data['agent_email'] && $this->model->query('agent')->duplicate($data['agent_email'])) {

                    $arr['error_message'] = "อีเมล์ไม่สามารถใช้ได้ (อีเมล์นี้ถูกใช้ไปแล้ว)";
                }
            }
        } else if ($data['field'] == 'agent_tel') {

            $arr['error_message'] = $form->verify('phone_number', $data['value']);
            if (empty($arr['error_message'])) {

                if ($item['agent_tel'] != $data['value'] && $this->model->query('agent')->duplicate($data['value'])) {
                    $arr['error_message'] = "เบอร์โทรศัพท์ม่สามารถใช้ได้ (เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว)";
                }
            }
        }

        if (empty($arr['error_message'])) {
            // seve 

            $post[$data['field']] = $data['value'];
            $this->model->query('agent')->update($id, $post);
        }

        $arr['error'] = !empty($arr['error_message']);

        echo json_encode($arr);
    }

}
