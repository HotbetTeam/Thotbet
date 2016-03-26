<?php

echo '<!doctype html>';

if( $this->elem("html")->attr() ){

    $attributes = "";
    foreach ($this->elem("html")->attr() as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    echo '<html'.$attributes.'>';
}
else{
    echo '<html>';
}

echo '<head>';

// Page title
if (isset($this->title)){
    echo '<title>' . PAGE_TITLE . '</title>';
}else{
    echo '<title></title>';
}

echo '<meta charset="utf-8" />';
echo '<link rel="shortcut icon" href="'.IMAGES.'favicon.png">';



echo $this->head('css');
echo $this->head('js');

// captcha
if( !empty($this->captcha) ){
    echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
}

echo '</head>';

if( $this->elem("body")->attr() ){

    $attributes = "";
    foreach ($this->elem("body")->attr() as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    echo '<body'.$attributes.'>';
	
}
else{
    echo '<body>';
}
?>