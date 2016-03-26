<div id="mainContainer" class="clearfix" data-plugins="main">

	<?php require 'sections/left.php'; ?>

	<div class="settings-content" role="content">
		<div class="settings-content-main" role="main"><?php

			if( !empty($this->section) ){

				require_once "sections/{$this->section}.php";
			}
			else{

				require_once "sections/init.php";
			}

		?></div>
	</div>
</div>