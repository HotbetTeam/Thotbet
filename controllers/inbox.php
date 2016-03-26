<?php

class Inbox extends Controller {

    function __construct() {
        parent::__construct();

        $this->view->theme = 'manage';
    }

    public function index($id=null){


        if( !empty($id) ){
            
        }

    	$this->view->currentPage = "inbox";
    	$this->view->recent = $this->model->query('messages')->lists();

    	$this->view->elem('body')->attr('data-options', $this->fn->stringify(array(
    		'username'=> $this->me['user_id'],
            'key_id' => $this->me['conversation_key_id'],
			'ROOT' => FIREBASE_SERVER,

			// 'ref'=> 'webMessenger',
			'URL'=> URL."inbox/",
			// 'limit'=>50,
			'sound'=>true,
			'url_sound'=>URL.'public/sounds/message.mp3',
			'current_id' => $id
    	)));
    	$this->view->elem('body')->attr('data-plugins', 'webMessenger');

    	$this->view->hasLeftCol = 'inbox/wmMasterView';
    	$this->view->render('inbox/wmMasterMain');
    }

    public function conversation($id='') {
        // sleep(2);
        echo json_encode( $this->model->getMessage( array('id'=>$id) ));
    }

    public function send() {
    	echo json_encode( $this->model->send() );
    }

    public function nav($section='recent') {
        
        // sleep(2);
        $section = 'nav_'.$section;
        echo json_encode( $this->model->{$section}() );
    }

}