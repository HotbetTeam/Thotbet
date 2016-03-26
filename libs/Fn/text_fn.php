<?php

class Text_Fn extends _function {
    
    public function example($text){
        return "example:".$text;
    }

    public function _config(){
        return $this;
    }
    // อักขระ
    public function characters($str){
        if(eregi("[\~\!\`\#\%\^\$\&\*\+-,\;\/\@\{\}\\\'\"\:\<\>\(\)\?]|\]|\[|\||฿", $str) )
        return false;
        
        else
        return true;
    }

    public function strip_tags_html($str){

        if(empty($str)) return "";
        $newstr = "";
        $str = nl2br(trim($str));
        $str = strip_tags($str, "<p><strong><b><br><ul><ol><li><u><blockquote>"); // <em>
        //$str = mysql_real_escape_string(htmlspecialchars($str));

        $order = array('\&quot;', '\"');
        $replace = '"'; //&quot;
        $newstr = str_replace($order, $replace, $str);

        $order = array('\&apos;', "\'");
        $replace = "'";
        $newstr = str_replace($order, $replace, $newstr);

        /*$order = array("\r\n", "\n", "\r");
        $replace = '<br />';
        $newstr = str_replace($order, $replace, $newstr);*/

        /*for ($j = 0; $j < 5; $j++) {
            $str_replace = "<br />";
            for ($i = 0; $i < 10; $i++) {
                $str_replace .= "<br />";
                $newstr = str_replace($str_replace, '<br />', $newstr);
            }
        }*/

        return trim($newstr);
    }
    
    public function strip_tags_br($text) { 

        $order = "<p>&nbsp;</p>";
        $replace = '<br>';

        $str = "Is your name O\'reilly?";

        $text = stripslashes($text);
        $text = str_replace($order, $replace, $text);

        $order = array("\r\n", "\n", "\r");
        $replace = '<br>';
        $text = str_replace($order, $replace, $text);

        for ($j = 0; $j < 5; $j++) {
            $str_replace = "<br>";
            for ($i = 0; $i < 10; $i++) {
                $str_replace .= "<br>";
                $text = str_replace($str_replace, '<br>', $text);
            }
        }
        return $text;
    }

    // '<a><ul><li><b><i><sup><sub><em><strong><u><br><br/><br /><p><h2><h3><h4><h5><h6>' ;
    public function strip_tags_editor($text, $allowed_tags = "<a><p><strong><b><ul><ol><li><u><blockquote><img>"){

        mb_regex_encoding('UTF-8');
        
        $text = nl2br(trim($text));
        $text = strip_tags($text, $allowed_tags);
        
        //replace MS special characters first
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);
        
        $attribute = array('style','onclick','onload');
        foreach($attribute as $attr){
            $text = preg_replace("/(<[^>]+) {$attr}=\".*?\"/i", '$1', $text);
        }
        
        // $text = preg_replace('/<img src="(.+?)">(.+?)<\/p>/i', "$2", $text);
        // $text = preg_replace('/<img', '$2', $text);
        // $text = stripArgumentFromTags($text);

        return $this->strip_tags_br($text); 
    }

    public function mb_ucfirst($str, $enc = 'utf-8') { 
        return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
    }

    public function textarea($str) {
        // $str = str_replace('<br />', "\n", $str);
        return strip_tags($str);
    }

    public function input($str){
        // htmlentities(string)
        return htmlentities($str);
    }

    public function more($str, $limit=150){
        return (strlen( strip_tags($str) ) > $limit)
            ? mb_substr($str, 0, $limit, 'utf-8')."..."
            : $str;
    }

    public function address($data) {
        $str = '';

        // บ้านเลขที่
        $str.= $data['number'];

        // หมู่ที่
        $str.= " ม.{$data['mu']}";

        // หมู่บ้าน
        $str.= " บ้าน{$data['village']}";

        // ซอย
        if( !empty($data['alley']) ){
            $str.= " ซ.{$data['alley']}";
        }

        // ถนน
        if( !empty($data['street']) ){

            if($data['street']!='-'){
                $str.= " ถ.{$data['street']}";
            }
            
        }
        
        

        // ตำบล
        $str.= " ต.{$data['district']}";

        // อำเภอ
        $str.= " อ.{$data['amphur']}";

        // จังหวัด
        $str.= " จ.{$data['province']}";

        // รหัสไปรษณีย์
        $str.= " {$data['zip']}";

        return $str;
    }

    public function hashtag($string){
        $htag = "#";
        $arr = explode(' ', $string);
        $arrc = count($arr);

        $i = 0;
        while ($i < $arrc) {
            
            if(substr($arr[$i], 0, 1) === $htag){
                $arr[$i] = '<a href="/hashtag/">'.$arr[$i].'</a>';
            }
            $i++;
        }

       $string = implode(" ", $arr);
       return $string;
    }
    
}