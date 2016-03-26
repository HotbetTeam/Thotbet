<?php

class _function {

    private $_q = array();
    public function q( $query ){

        if(array_key_exists($query, $this->_q)==false){
            require_once "Fn/{$query}_fn.php";
            $_fn = $query . '_Fn';
            $this->_q[$query] = new $_fn;
        }

        return $this->_q[$query];
    }

    public function text($fn=null, $text=null){

        if(!empty($fn))
        return $this->q('text')->{$fn}($text);

        else
        return $this->q('text')->_config();        
    }

    // connect: user.fn
    public function listbox_user($result){
        return $this->q('user')->_config('listbox', $result);
    }

    // connect group.fn
    public function listbox_group($result){
        return $this->q('group')->_config('listbox', $result);
    }
    public function listbox_groupUser($result){
        return $this->q('group')->_config('listbox_user', $result);
    }

    // fun: default 
	public function stringify($data){
        return htmlentities(json_encode($data));
    }

	public function avatar($fileImage, $figSize=48, $pathfile = null, $alt = null) {
        $figSize_h = $figSize - 1;
        $img = "";
        if ($fileImage) {

            if ($pathfile) {
                $file = ROOT . DS . 'public' . DS . str_replace('/', DS, $pathfile) . DS . $fileImage;
                $src = URL . "public/" . $pathfile . '/' . $fileImage;
            } else {
                $file = ROOT . DS . 'public' . DS . 'images' . DS . 'avatar' . DS . $fileImage;
                $src = URL . "public/images/avatar/" . $fileImage;
            }
            //echo $file;
            if (file_exists($file)) {
                $editPic = '';

                $size = getimagesize($file);
                $width = $size[0];
                $height = $size[1];

                if ($width > $height) {
                    $editPic = ($width * $figSize) / $height;

                    if ($editPic > $figSize_h) {
                        $editPic -= $figSize;
                        $editPic /= 2;
                        $editPic = '-' . $editPic;
                    } else {
                        $editPic = ($figSize - $editPic) / 2;
                    }
                }

                $cls = ($width < $height) ? 'scaledImageFitWidth img' : 'img';
                $st = ($editPic != '') ? ' style="left:' . floor($editPic) . 'px"' : '';

                $alt = ($alt) ? ' alt="' . $alt . '" ' : '';

                $img .= '<img class="' . $cls . '" src="' . $src . '"' . $alt . $st . '>';
            }
        } else {
            $pic = (80 * $figSize) / 80;
            $editPic = ($figSize - $pic) / 2;
            $img .= '<img class=" img" src="' . URL . 'public/images/avatar/error/error.png" style="left:' . floor($editPic) . 'px">';
        }

        return $img;
    }

    public function addClass($class=null){

        $str = "";
        if(!empty($class)){
            if(is_array($class)){
                foreach ($class as $value) {
                    $str .= !empty($str)? " ":"";
                    $str .= $value;
                }
            }else{
                $str = $class;
            }

            $str = ' class="'.$str.'"';
        }

        return $str;
    }

    public function hiddenInput($data=array()){
        $html = "";
        foreach ($data as $key => $value) {

            $class = array();
            $class[] = "hiddenInput";
            if(!empty($value['addClass'])){
                $class[] = $value['addClass'];
            }
            $class = self::addClass($class);

            $name = "";
            if(!empty($value['name'])){
                $name = ' name="'.$value['name'].'"';
            }

            $val = "";
            if(!empty($value['value'])){
                $val = ' value="'.$value['value'].'"';
            }

            $id = "";
            if(!empty($value['id'])){
                $id = ' id="'.$value['id'].'"';
            }

            $html.='<input'.$class.' type="hidden" autocomplete="off"'.$name.$val.$id.'>';
        }

        return $html;
    }

    public function set_hiddenInput(&$hiddenInput, $data, $key=null){
        
        foreach ($data as $name=>$value) {
            if(is_array($value)){
                self::set_hiddenInput( $hiddenInput, $value, $name);
            }
            else{
                if( $key ){
                    $hiddenInput[] = array( "name"=> $key.'['.$name.']', "value"=>$value );
                }
                else{
                    $hiddenInput[] = array( "name"=>$name, "value"=>$value );
                }
            }
        }     
    }

    // stepList
    public static function stepList($lists = array(), $select = null, $show_number = true, $style_line = false) {

        $str = "";
        foreach ($lists as $i => $val) {
            $selected = $val['name'] == $select ? ' uiStepSelected' : '';
            $str .= '<li class="uiStep' . $selected . '">' .
                    '<div class="part back"><span class="arrowBorder"></span><span class="arrow"></span></div>' .
                    '<div class="part middle"><div class="content">' .
                    (!empty($val['link']) ? '<a href="' . $val['link'] . '" class="title">' : '<span class="title">') .
                    ($show_number ? '<span class="fwb">' . ($i + 1) . '.</span> ' : '' ) . $val['text'] .
                    (!empty($val['link']) ? '</a>' : '</span>') .
                    '</div></div>' .
                    '<div class="part point"><span class="arrowBorder"></span><span class="arrow"></span></div>' .
                    '</li>';
        }

        return '<div class="uiStepList' . ($style_line ? ' uiStepListSingleLine' : '') . '"><ol>' . $str . '</ol></div>';
    }

    public function getURL($options, $set=null, $val=null){

        $get = "";
        foreach ($options as $key => $value) {

            if($key==='url') continue;

            $value = $set==$key? $val: $value;

            if(isset($value)){
                $get.=empty($get)? "?":"&";
                $get.="$key=$value";
            }
        }

        return URL.$options['url']."/{$get}";
    }

    public function actionPager($options=null)
    {
        # pager
        $pager = array(
            'length'=>$options['pager']
        );

        $pager['prev'] = $pager['length']-1;
        $pager['prev_url'] = $this->getURL($options['geturl'], "pager", ($pager['length']-1));
        $pager['prev_btn'] = $pager['prev']<1
            ? '<a class="phs btn disabled" href="#"><i class="icon-chevron-left"></i></a>'
            : '<a class="phs btn" href="'.$pager['prev_url'].'"><i class="icon-chevron-left"></i></a>';

        $pager['next'] = $pager['length']+1;
        $pager['next_url'] = $this->getURL($options['geturl'], "pager", ($pager['length']+1));
        $pager['next_btn'] = '<a class="phs btn" href="'.$pager['next_url'].'"><i class="icon-chevron-right"></i></a>';

        $limit = $options['limit'];
        $start_limit = ($limit*$pager['length'])-$limit+1;
        $end_limit = $limit*$pager['length'];

        if($end_limit>=$options['count']){
            $end_limit = $options['count'];
            $pager['next_btn'] = '<a class="phs btn disabled" href="#"><i class="icon-chevron-right"></i></a>';
        }

        if( !empty($options['is_disabled']) ){
            $pager['prev_btn'] = '<a class="phs btn disabled" href="#"><i class="icon-chevron-left"></i></a>';
            $pager['next_btn'] = '<a class="phs btn disabled" href="#"><i class="icon-chevron-right"></i></a>';
        }

        return $options['count']!=0
            ? '<li class="r group-btn">'.
                '<span class="mhs fcg">'.$start_limit.'-'.$end_limit.' จาก '.$options['count'].'</span>'.
                $pager['prev_btn']. $pager['next_btn'].
              '</li>'
            : "";
    }
	
    // PHP 5.3-
    function birthday($birthday){ 
        $age = strtotime($birthday);
    
        if($age === false){ 
            return false; 
        } 
        
        list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
        
        $now = strtotime("now"); 
        
        list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
        
        $age = $y2 - $y1; 
        
        if((int)($m2.$d2) < (int)($m1.$d1)) 
            $age -= 1; 
            
        return $age; 
    }

    // PHP 5.3+
    /*function birthday($birthday) {
        $age = date_create($birthday)->diff(date_create('today'))->y;
        
        return $age;
    }*/

    public function spinner(){
        
        $circle = '';
        for ($i=1; $i <= 12; $i++) { 
            $circle.='<div class="sk-child sk-circle'.$i.'"></div>';
        }
        return '<div class="sk-circle">'. $circle.'</div>';
    }

    public function imageCoverBox($url, $size=851, $theSize=array(851, 315)){
        
        $width = $size;
        $height = round( ($theSize[1]*$size) /$theSize[0], 2 );
        return '<div class="avatar-cover" style="width:'.$width .'px;height:'.$height.'px"><img src="'.$url.'" /></div>';
    }
    public function imageBox($url, $size=640, $theSize=array(640, 360)){
        $width = $size;
        $height = round( ($theSize[1]*$size) /$theSize[0], 2 );
        return '<div class="avatar-cover" style="width:'.$width .'px;height:'.$height.'px"><img src="'.$url.'" /></div>';
    }
}