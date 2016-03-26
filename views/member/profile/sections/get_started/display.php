<?php 

require 'init.php';
require 'toolbar_step.php';
require 'form.php';

?>

<div class="profile-content" role="content">

	<div role="toolbar"><div class="clearfix"><?=$step_html?></div></div>

<div class="profile-main" role="main">


<form class="js-submit-form mal" method="post" action="<?=URL?>employees/<?=$this->step?>/<?=$this->item['emp_id']?>">

	<!-- set input -->
	<input type="hidden" autocomplete="off" name="id" value="<?=$this->item['emp_id']?>" />
	<input type="hidden" autocomplete="off" name="step" value="<?=$this->step?>" />
	<?php
		if( !empty($this->next) ){
			echo '<input type="hidden" autocomplete="off" name="next" value="'.$this->next.'">';
		}
	?>

	<div class="mvl ptm">

		<div class="phl">
			<h2 class="mbs"><?=$this->title?></h2>

			<?=$form_html?>

			<div class="clearfix mtl">
				<button type="submit" class="btn btn-blue">บันทึก</button>
			</div>
		</div>
	</div>

	
</form>

</div></div>