<?php

class Operator extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->formatPage = 'manage';
    }

    public function index() {
        
        $nav[] = array( 'text' => 'Operator' );
        $this->view->nav = $nav;
        $this->view->currentPage = 'operator';
        $this->view->results = $this->model->query('operator')->lists();

        // $this->view->css('wcss');
        

        if( $this->format=='json' ){
            // sleep(1);
            $this->view->render('operator/lists/json');
        }
        else{
            $this->view->elem('body')->addClass('hidden-tobar');
            $this->view->render('operator/lists/display');
        }
    }

    public function form($id = 0) {        
        if (!empty($id)) $this->view->item = $this->model->query('operator')->get($id);
     
        $this->view->render('operator/dialog/form');
        // echo json_encode($x);
    }

    public function set($id = null) {
        if (empty($this->me) || $this->format != 'json' || empty($_POST)) $this->error();

        try {
            $form = new Form();
            $form   ->post('op_customer')->val('is_empty')
                    ->post('op_text')->val('is_empty');

            $form->submit();
            $arr['data'] = $form->fetch();
        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $id;
        if(!empty($id)){ 
            $item = $this->model->query('operator')->get($id);
        }
         
         
        if (empty($arr['error'])) {
            $arr['data']['op_user_id'] = $this->me['user_id'];
            $arr['data']['op_date'] = date('Y-m-d H:i:s');
            
            // update
            if( !empty($id) ){
                $lastid = $this->model->update($id, $arr['data']);
            }
            // insert
            else{
                $lastid = $this->model->insert($arr['data']);
            }
            
            $arr['url'] = 'refresh';
        }

        echo json_encode($arr);
    }
    
    function del($id=null){
        
        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( empty($this->me) || $this->format!='json' || empty($id) ) $this->error();

        $item = $this->model->query('operator')->get( $id );
        if( empty($item) ) $this->error();

        if( !empty($_POST) ){

            $this->model->delete( $id );
            $arr['message'] = "ลบเรียบร้อย";
            $arr['url'] = "refresh";
            echo json_encode($arr);
        }
        else{
            $this->view->item = $item;
            $this->view->render('operator/dialog/form_del');
        }
    }
}