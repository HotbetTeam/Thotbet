<?php

class Playing extends Controller {

    function __construct() {
        parent::__construct();
        $this->view->theme = 'manage';
    }

    public function index() {

        $nav[] = array('text' => 'ประวัติการเล่นของสมาชิก');
        $this->view->nav = $nav;
        $this->view->currentPage = 'playing';

        // print_r( $this->model->query('playing')->lists() ); die;
        $this->view->results = $this->model->query('playing')->lists();

        if( $this->format=='json' ){
            // sleep(1);
            $this->view->render('playing/lists/json');
        }
        else{
            $this->view->elem('body')->addClass('hidden-tobar');
            $this->view->render('playing/lists/display');
        }
    }

    public function form($id=null) {
        
        if( !empty($id) ){
            $item = $this->model->query('playing')->get($id);
            if(empty($item)) $this->error();

            $post = array_merge($item, $_GET);
            $this->view->item = $item;
            $this->view->post = $post;
        }

        if( isset($_REQUEST['user']) ){
            $member = $this->model->getMember( $_REQUEST['user'] );
            $this->view->member = $this->model->query('member')->get( $member['m_id'] );
        }

        $this->view->render('playing/dialog/form');
    }
    
    public function update($id=null) {
        if( empty($this->me) || $this->format!='json' || empty($_POST) ) $this->error();

        $id = isset($_REQUEST['id']) ? $_REQUEST['id']: $id;
        if( !empty($id) ){
            $item = $this->model->query('playing')->get($id);
            if(empty($item)) $this->error();
        }

        // sleep(1);
        try {
            $form = new Form();
            $form   ->post('pl_wagers')->val('numeric')->val('is_empty')
                    ->post('pl_bet_amount')->val('numeric')->val('is_empty')
                    ->post('pl_menber')->val('numeric')->val('is_empty')
                    ->post('pl_actual')->val('numeric')->val('is_empty');

            $form->submit();
            $dataPost = $form->fetch();

            $playing = array(
                'pl_wagers' => floor($dataPost['pl_wagers']),
                'pl_bet_amount' => substr($dataPost['pl_bet_amount'],0,strpos($dataPost['pl_bet_amount'],'.')+3),
                'pl_menber' => substr($dataPost['pl_menber'],0,strpos($dataPost['pl_menber'],'.')+3),
                'pl_actual' => substr($dataPost['pl_actual'],0,strpos($dataPost['pl_actual'],'.')+3)
            );

            // insert
            if( empty($id) ){

                $dataPost['user'] = isset($_REQUEST['user']) ? $_REQUEST['user']: null;
                if( empty($dataPost['user']) ){
                    $arr['error']['user'] = 'กรอกรหัสสมาชิก';
                }
                else{

                    $dataPost['user'] = str_replace("&nbsp;",'', htmlentities( $dataPost['user'], null, 'utf-8') );
                    $dataPost['user'] = trim( strip_tags($dataPost['user']) );

                    $member = $this->model->getMember( $dataPost['user'] );
                    if( empty($member) ){
                        
                        $member = array(
                            'game_user'=> $dataPost['user'],
                            'm_name'=> $dataPost['user'],
                            'm_status' => 'play'
                        );
                        $this->model->query('member')->insert( $member );
                    }

                    $date = date('Y-m-d', strtotime($_POST['pl_date']));
                    $plId = $this->model->checkDate( $member['m_id'], $date );
                    
                    // check date
                    if( !$plId ){

                        $playing['pl_m_id'] = $member['m_id'];
                        $playing['pl_date'] = $date;

                        // add now call post
                        $point = $this->model->query('member')->getPoint();
                        $playing['pl_menber_point'] = $point['menber'];
                        $playing['pl_actual_point'] = $point['actual'];

                        // new playing
                        $this->model->query('playing')->insert( $playing );

                        // update Poit To member
                        $poit['menber'] = isset( $member['point'] ) ? $member['point'] : 0;

                        $poit['x5'] = $playing['pl_menber'] * ($point['menber']*-1);
                        $poit['x02'] = $playing['pl_actual'] * $point['actual']; 

                        $poit['sumpoit'] = ($poit['menber'] + ($poit['x5']+$poit['x02'] ));

                        if($poit['menber'] < $poit['sumpoit']){
                            $this->model->query('member')->update($member['m_id'],array('m_point_show'=>$poit['sumpoit']));
                        }

                        $this->model->query('member')->update($member['m_id'],array(
                            'm_point'=>$poit['sumpoit'],
                            'm_updated' => date('c')
                        ));

                        // Update Level To member
                        $this->model->query('member')->upLevel( $member['m_id'], $poit['sumpoit'] );


                        // return call back
                        $arr['message'] = 'เพิ่มข้อมูลการเล่นเรียบร้อย';
                        $arr['url'] = URL.'member/'.$member['m_id'];
                    }
                    else{

                        $date_str = $this->fn->q('time')->normal( strtotime($date) );
                        $arr['error']['user'] = "{$dataPost['user']} มีข้อมูลการเล่นของวันที่ {$date_str} แล้ว<br> คุณต้องการแก้ไขข้อมูลนี้หรือไม่ " . '<a href="'.URL.'playing/form/'.
                            $plId.
                            '?pl_wagers='.$dataPost['pl_wagers'].
                            '&pl_bet_amount='.$dataPost['pl_bet_amount'].
                            '&pl_menber='.$dataPost['pl_menber'].
                            '&pl_actual='.$dataPost['pl_actual'].
                            '&pl_date='.$date.
                        '" data-plugins="dialog">แก้ไข</a>';
                    }
                } // end empty user
            }

            // update
            else{
                
                $this->model->query('playing')->update( $id, $playing );

                $arr['message'] = 'แก้ไขข้อมูลการเล่นเรียบร้อย';
                $arr['url'] = $item['url'];
            }

        } catch (Exception $e) {
            $arr['error'] = $this->_getError($e->getMessage());
        }

        echo json_encode($arr);
    }

    public function upload() {

        if( $this->format!='json' ) $this->error();

        if( !empty($_FILES['file1']) ){

            $upload = new Upload();
            $upload->current = $_FILES['file1'];
            if( $upload->validate( $err, array('type' => 'excel') ) ){

                require_once WWW_LIBS.'PHPExcel.php';
                $inputFileName = $upload->current['tmp_name'];
                try {

                    // Import data form excel file
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $reader = PHPExcel_IOFactory::createReader($inputFileType);
                    $reader->setReadDataOnly(true);
                    $excel = $reader->load($inputFileName);

                    $worksheet = $excel->setActiveSheetIndex(0);
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();

                    $headingsArray = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                    $headingsArray = $headingsArray[1];

                    $r = -1;
                    $namedDataArray = array();
                    for ($row = 2; $row <= $highestRow; ++$row) {
                        $dataRow = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                        if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                            ++$r;
                            foreach ($headingsArray as $columnKey => $columnHeading) {

                                $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                            }
                        }
                    }

                    // ลืม เช็คว่าถ้าไม่มีข้อมูล $namedDataArray

                    // 
                    $date = date('Y-m-d', strtotime($_POST['date']));
                    // $members = array();
                    foreach ($namedDataArray as $key => $value) {
                        if(empty($value['MemberAccount'])) continue;
                        $username = str_replace("&nbsp;",'', htmlentities( $value['MemberAccount'], null, 'utf-8') );

                        $username = trim( strip_tags($username) );

                        $member = $this->model->getMember( $username );
                        if( empty($member) ){
                        
                            $member = array(
                                'game_user'=> $username,
                                'm_name'=> $username,
                                'm_status' => 'play'
                            );
                            $this->model->query('member')->insert( $member );
                        }
                        
                        if( empty($member['m_id']) ) continue;

                        $playing = array(
                            'pl_m_id' => $member['m_id'],
                            'pl_wagers' => $value['Wagers'],
                            'pl_bet_amount' => $value['Betamount'],
                            'pl_menber' => $value['Member'],
                            'pl_actual' => $value['ActualbettingQty'],
                            'pl_date' => $date
                        );

                        $plId = $this->model->checkDate( $member['m_id'], $date );

                        // check date
                        if( $plId ){
                            // new update
                            $this->model->query('playing')->update( $plId, $playing );
                            $this->model->query('member')->update($member['m_id'], array());
                        }
                        else{

                            // add now call post
                            $point = $this->model->query('member')->getPoint();
                            $playing['pl_menber_point'] = $point['menber'];
                            $playing['pl_actual_point'] = $point['actual'];

                            // new playing
                            $this->model->query('playing')->insert( $playing );

                            // update Poit To member
                            $poit['menber'] = isset( $member['point'] ) ? $member['point'] : 0;

                            $poit['x5'] = $playing['pl_menber'] * ($point['menber']*-1);
                            $poit['x02'] = $playing['pl_actual'] * $point['actual']; 

                            $poit['sumpoit'] = ($poit['menber'] + ($poit['x5']+$poit['x02'] ));

                            if($poit['menber'] < $poit['sumpoit']){
                                $this->model->query('member')->update($member['m_id'],array('m_point_show'=>$poit['sumpoit']));
                            }

                            $this->model->query('member')->update($member['m_id'],array(
                                'm_point'=>$poit['sumpoit'],
                                'm_updated' => date('c')
                            ));

                            // Update Level To member
                            $this->model->query('member')->upLevel( $member['m_id'], $poit['sumpoit'] );

                        }

                    }

                    $arr['message'] = 'อัพโหลดข้อมูลการเล่นเสร็จเรียบร้อย';
                    $arr['url'] = URL.'playing';

                } catch(Exception $e) {

                    $arr['error']['file'] = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
                }

            }
            else{

                $arr['error']['file'] = $err;
            }

            // require_once 'Fn/excel_reader2.php';
            
            echo json_encode($arr);
        }
        else{
            $this->view->render('playing/dialog/form_upload');
        }
    }

}
