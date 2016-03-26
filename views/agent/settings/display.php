<?php require_once 'init.php'; ?>
<div id="mainContainer" class="clearfix" data-plugins="main">

	<?php require 'sections/left.php'; ?>

	<div class="settings-content" role="content">
		<div class="settings-content-main" role="main"><?php

			if( !empty($this->section) ){

				require_once "sections/{$this->section}.php";
			}

		?></div>
	</div>
</div>