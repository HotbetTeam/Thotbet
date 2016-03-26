<?php


class Manage_Theme extends View {
	
	
	function __construct( $t ) {
		parent::__construct( $t );

		// $this = $t;
		print_r($this); die;
	}

	public function main($context) {

		$this->_dir['main'] = $context;
		return $this;
	}

	public function display() {

		// 
        # topbar
        require WWW_VIEW. "Layouts/topbar/manage.php";

        require WWW_VIEW. 'Layouts/sidebar/navigation-main.php';

        # content
        echo '<div id="container"><div id="content" role="main">';
        require WWW_VIEW . $this->_dir['main'] . '.php';
        echo '</div></div>';
	}
}