<?php

class Controller {

    public $format = "html";
    public $pathName = "";
    public $logPage = array('inbox', 'member', 'playing', 'manage', 'operator', 'users');

    function __construct() {

        $this->fn = new _function();
        $this->format = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? "json" : "html";

        $this->view = new View();
        $this->view->format = $this->format;
    }

    /**
     * 
     * @param string $name Name of the model
     * @param string $path Location of the models
     */
    public function loadModel($name, $modelPath = 'models/') {

        $path = $modelPath . $name . '_model.php';
        $this->pathName = $name;

        if (file_exists($path)) {
            require $modelPath . $name . '_model.php';

            $modelName = $name . '_Model';
            $this->model = new $modelName();
            $this->handleLogin();
        }
    }

    protected function _getError($err) {
        $err = explode(',', rtrim($err, ','));

        $error = array();
        foreach ($err as $k) {
            $str = explode('=>', $k);
            $error[$str[0]] = $str[1];
        }

        return $error;
    }

    public $me = null;

    public function handleLogin() {
        // Session::init();
        // admin
        if (in_array($this->pathName, $this->logPage)) {

            if (Cookie::get(COOKIE_KEY_ADMIN)) {
                $me = $this->model->getUser(Cookie::get(COOKIE_KEY_ADMIN));
            }

            if (!empty($me)) {
                $this->me = $me;
                $this->model->me = $this->me;
                $this->view->me = $this->me;

                Cookie::set(COOKIE_KEY_ADMIN, $this->me['user_id']);
            } else {
                $this->login('admin');
            }
        }
        // member
        else {

            if (Cookie::get(COOKIE_KEY)) {
                $me = $this->model->query('member')->get(Cookie::get(COOKIE_KEY));
            }

            if (!empty($me)) {
                $this->me = $me;
                $this->model->me = $this->me;
                $this->view->me = $this->me;
                Cookie::set(COOKIE_KEY, $this->me['m_id']);

                if ($this->pathName == 'login') {
                    header("Location:" . URL);
                }
            } else {
                $this->view->elem('body')->addClass('loggedOut');
            }
        }
    }

    public function login($page = '') {

        if (in_array($this->pathName, $this->logPage)) {
            Session::init();
            $attempt = Session::get('login_attempt');

            if (isset($attempt) && $attempt >= 2) {
                // $this->view->captcha = true;   
            } elseif (empty($attempt)) {
                $attempt = 0;
                Session::set('login_attempt', $attempt);
            }
        }

        $path_admin = !empty($_REQUEST['path_admin']) ? $_REQUEST['path_admin'] : false;
        if (!empty($_POST)) {

            try {
                $form = new Form();

                $form->post('email')->val('is_empty')
                        ->post('pass')->val('is_empty');

                $form->submit();
                $post = $form->fetch();

                // admin
                if (in_array($this->pathName, $this->logPage) || $path_admin) {

                    $id = $this->model->query('users')->login($post['email'], $post['pass']);

                    if (!empty($id)) {

                        Cookie::set(COOKIE_KEY_ADMIN, $id);

                        if (isset($attempt)) {
                            Session::clear('login_attempt');
                        }

                        $url = !empty($_REQUEST['next']) ? $_REQUEST['next'] : $_SERVER['REQUEST_URI'];

                        header('Location: ' . $url);
                    } else {

                        if (!$this->model->query('users')->is_user($post['email'])) {
                            $error['email'] = 'ชื่อผู้ใช้ไม่ถูกต้อง';
                        } else {
                            $error['pass'] = 'รหัสผ่านไม่ถูกต้อง';
                        }
                    }
                }
                // member
                else {

                    if ($post['pass'] == '')
                        $post['pass'] = HASH_PASSWORD_KEY;
                    $id = $this->model->query('member')->login($post['email'], $post['pass']);
                    if (!empty($id)) {

                        Cookie::set(COOKIE_KEY, $id);

                        $url = !empty($_REQUEST['next']) ? $_REQUEST['next'] : $_SERVER['REQUEST_URI'];

                        header('Location: ' . $url);
                    } else {

                        if (!$this->model->query('member')->is_user($post['email'])) {
                            $error['email'] = 'ชื่อผู้ใช้ไม่ถูกต้อง';
                        } else {
                            $error['pass'] = 'รหัสผ่านไม่ถูกต้อง';
                        }
                    }
                }

                $post['pass'] = "";
                $this->view->post = $post;
            } catch (Exception $e) {
                $error = $this->_getError($e->getMessage());
            }
        }


        if (!empty($error)) {

            if (in_array($this->pathName, $this->logPage)) {
                $attempt++;
                Session::set('login_attempt', $attempt);
            }

            $this->view->error = $error;
        }

        $this->view->next = !empty($_REQUEST['next']) ? $_REQUEST['next'] : $_SERVER['REQUEST_URI'];


        $this->view->elem('body')->addClass('login_page');
        if (in_array($this->pathName, $this->logPage) || $path_admin) {
            $this->view->css('login');
            $this->view->setPageOptions('topbar', false);
            $this->view->formatPage = 'login';
            $this->view->render("Layouts/page/login");
        } else {
            $this->view->render("member/login");
        }

        exit;
    }

    public function error() {

        $this->loadModel('error');

        $this->view->elem('body')->addClass('error_page');
        $this->view->css('error');
        $this->view->render("Layouts/page/error");
        exit;
    }

}
