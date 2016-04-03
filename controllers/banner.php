<?php

class Banner extends Controller {

    function __construct() {
        parent::__construct();

    }
    
    public function index() {

        $this->error();
    }

    public function upload() {
        
        if( !empty($_FILES['file1']) ){

            $userfile = $_FILES['file1'];
            $upload = new Upload;
            $upload->current = $userfile;
            $arr['error_message'] = '';
            $upload->validate( $arr['error_message'], array('type'=>'image') );

            if( empty($arr['error_message']) ){

                $type = $upload->getType($userfile['name']);

                $data['banner_width'] = $_POST['width'];
                $data['banner_height'] = $_POST['height'];
                $data['banner_caption'] = $_POST['caption'];

                $filename = "{$data['banner_width']}x{$data['banner_height']}.{$type}";
                $data['banner_image_url'] = $filename;

                $this->model->query('banner')->update( $data );

                $dest = WWW_IMAGES."banner/{$filename}";

                if( $upload->copies( $userfile['tmp_name'], $dest ) ){
                    $arr['message'] = "เพิ่มเรียบร้อย";
                    $arr['url'] = "refresh";

                    $arr['src'] = IMAGES.'banner/'.$filename.'?rand='.rand();
                }
                else{
                    $arr['error_message'] = $arr['การอัพโหลดไฟล์ล้มเหลว'];

                    // 
                }
                
            }
            else{
                $arr['error_message'] = $arr['การอัพโหลดไฟล์ล้มเหลว'];
            }

        }
        else{
            $arr['error_message'] = 'กรุณาเลือกไฟล์';
        }
        

        if( !empty($arr['error_message']) ){
            $arr['error'] = 1;
            $arr['message'] = array( 'text' => $arr['error_message'], 'load' => true, 'sleep' => 10000, 'bg' => 'red' ) ;
        }

        echo json_encode($arr);
    }

    

}