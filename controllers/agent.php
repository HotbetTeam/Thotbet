<?php

class Agent extends Controller {

    function __construct() {
        parent::__construct();
        $this->view->formatPage = 'agent';
    }

    public function index() {
        $this->view->currentPage = 'agent';
        $this->view->elem('body')->addClass('col-x');
        // $this->view->js('casino');
        $this->view->render('agent/index');
    }

    public function register() {
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
        header('Location: '.URL.'register');
    }

}
