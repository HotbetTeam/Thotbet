<div role="content">
<?php
if( $this->me['dep_id']==3 ){ ?>
	<div data-alert data-load="<?=URL?>employees/mark_working/<?=$this->item['emp_id']?>"></div>
<?php } ?>
	<div class="profile-main" role="main">
		<div class="uiBoxWhite mal" style="max-width:500px">
			<div class="empty">
				<div class="empty-icon"><i class="icon-map-marker"></i></div>
				<div class="empty-title">กำหนดจุดทำงานให้กับพนักงาน</div>
				<?php if( $this->access['edit'] ){ ?>
				<div class="empty-message">
					<div class="mbm">ไม่มีการกำหนดจุดทำงานให้กับพนักงาน</div>
					<a href="<?=URL?>employees/mark_working/<?=$this->item['emp_id']?>" data-plugins="dialog" class="btn btn-success">กำหนดจุดทำงาน</a>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>