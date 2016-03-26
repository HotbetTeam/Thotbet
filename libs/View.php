<?php

class View {

    public $pageOptions = array();
    public $currentPage = '';
    public $theme = 'default';

    public function setPageOptions($key, $value = true) {
        $this->pageOptions[$key] = $value;
    }

    function __construct() {

        $this->fn = new _function();
        $this->ui = new uiElement();
    }

    public function render($name, $include = false, $content = null) {

        if ($include === true || $this->format === "json") {
            require 'views/' . $name . '.php';
        } else {

            // set page Dafault
            $this->initPage();
            # head
            require 'views/Layouts/default/head.php';

            # start: doc
            echo '<div id="doc">';

                if( !in_array($this->theme, array('manage')) ){
                    $this->theme = "dafault";
                }elseif( in_array($this->theme, array('login')) ){
                    $this->theme = "full";
                }

                $themeName = "getTheme_{$this->theme}";
                $this->{$themeName}( $name );

            # end: doc
            echo '</div>';

            # footer
            require 'views/Layouts/default/footer.php';
        }
    }

    public function getTheme_full($name) {

        # content
        echo '<div id="container"><div id="content">';
        require 'views/' . $name . '.php';
        echo '</div></div>';
    }

    public function getTheme_manage($name) {

        # banners
        // require "views/Layouts/banners/default.php";
        # topbar
        require "views/Layouts/topbar/manage.php";

        require 'views/Layouts/sidebar/navigation-main.php';


        // Left Colum
        if (!empty($this->hasLeftCol)) {

            echo '<div id="leftCol">';
            require "views/{$this->hasLeftCol}.php";
            echo '</div>';
        }


        # content
        echo '<div id="container"><div id="content">';
        require 'views/' . $name . '.php';
        echo '</div></div>';
    }

    private function getTheme_dafault($name) {

        # topbar
        require "views/Layouts/topbar/default.php";

        # content
        echo '<div id="page-container"><div id="page-main">';
        require 'views/' . $name . '.php';
        echo '</div></div>';

        #footer
        require "views/Layouts/footer/default.php";

        #chat
        echo '<div id="sidebar">';
        require 'views/Layouts/banners/right.php';
        require "views/Layouts/sidebar/chat.php";
        echo '</div>';

        if (!empty($this->me)) {

            if ($this->theme == 'home' && empty($this->me['phone_number'])) {
                echo '<div data-alert data-load="' . URL . 'alert/up/phone_number/"></div>';
            }
        }
    }

    public function initPage() {

        if ($this->theme == 'manage') {
            $this->elem('body')->addClass('blackSide'); //balance
            // $this->elem('body')->addClass('is-pushed-left');

            if (!empty($this->hasLeftCol)) {
                $this->elem('body')->addClass('hasLeftCol');
            }

            Session::init();
            $isPushedLeft = Session::get('isPushedLeft');

            if (isset($isPushedLeft)) {
                // echo $isPushedLeft; die;
                if ($isPushedLeft == 1) {
                    $this->elem('body')->addClass('is-pushed-left');
                }
            } else {
                $this->elem('body')->addClass('is-pushed-left');
            }

            $this->css('manage')->js('manage');
        } elseif (in_array($this->theme, array('login'))) {
            $this->css('manage');
        } else {
            $this->css('main')->js('main');
        }

        $this
                // ->css('icons-1')
                // ->css('fonts')
                ->css('default')
                ->js('firebase')
                ->js('custom')
                ->js('plugins/dialog')
                ->js('plugins/default')

                // ->js('hello/hello')
                ->js('jquery/jquery.autosize')
                ->js('jquery/jquery');

        // set option page
        /* $optionsDefault = array('topbar'=>'default'); // 'banners',

          foreach ($optionsDefault as $key => $value) {
          // echo $key;
          if( !isset($this->pageOptions[$key]) ){
          $this->setPageOptions($key, $value);
          }
          } */
    }

    public function error() {
        // render
        require 'views/error/index.php';
    }

    /* Elem : */

    private $_elem = array();
    private $_currentElem = null;

    // private $_attributes = array();
    // elem
    public function elem($elem = null) {
        $this->_currentElem = $elem;
        $this->_elem[] = $elem;
        return $this;
    }

    public function addClass($class) {
        $this->attr('class', $class);

        return $this;
    }

    public function hasClass($class) {

        $_currentClass = isset($this->_elem[$this->_currentElem]['attr']['class']) ? $this->_elem[$this->_currentElem]['attr']['class'] : null;

        if ($_currentClass) {
            if (in_array($class, explode(" ", $_currentClass)))
                return true;
            else
                return false;
        } else
            return false;
    }

    // attributes
    public function attr($attr = null, $value = null) {

        if ($attr) {
            if (is_string($attr)) {

                if ($value) {

                    if (isset($this->_elem[$this->_currentElem]['attr'][$attr])) {

                        $this->_elem[$this->_currentElem]['attr'][$attr] .= ( $this->hasClass($attr) ) ? "" : " " . $value;
                    } else {
                        $this->_elem[$this->_currentElem]['attr'][$attr] = $value;
                    }

                    return $this;
                } else {

                    if (isset($this->_elem[$this->_currentElem]['attr'][$attr]))
                        return $this->_elem[$this->_currentElem]['attr'][$attr];
                }
            }
            elseif (is_array($attr)) {
                $this->_elem[$this->_currentElem]['attr'] = $attr;
                return $this;
            }
        } else {
            if (isset($this->_elem[$this->_currentElem]['attr']))
                return $this->_elem[$this->_currentElem]['attr'];
        }
    }

    /*
      Head :
     */

    private $_head = null;

    public function css($link) {
        $this->_head['css'][] = $link;
        return $this;
    }

    public function js($src) {
        $this->_head['js'][] = $src;
        return $this;
    }

    public function head($name) {

        $elem = "";
        if (isset($this->_head[$name])) {

            for ($i = count($this->_head[$name]) - 1; $i >= 0; $i--) {
                switch ($name) {
                    case 'css':
                        $elem .='<link rel="stylesheet" type="text/css" href="' . CSS . $this->_head[$name][$i] . '.css">';
                        break;

                    case 'js':
                        $elem .='<script type="text/javascript" src="' . JS . $this->_head[$name][$i] . '.js"></script>';
                        break;
                }
            }
        }

        return $elem;
    }

}
