<?php

$options = array(
	'startDate' => $this->item['created'],
	'load_tasks_url' => URL.'employees/get_tasks/'.$this->item['emp_id'],
	'add_tasks_url' => URL.'employees/set_tasks/'.$this->item['emp_id'],
);
//  
// <div role="toolbar"><?php require 'sections/toolbar.php'; </div>
?>
<div class="profile-content" role="content" data-plugins="tasks" data-options="<?=$this->fn->stringify($options)?>">
	
	<div role="toolbar"><?php require 'toolbar.php'; ?></div>
	<div class="profile-main" role="main">

		<div role="calendar"></div>
		
	</div>
</div>