<div class="profile-content" role="content">
<?php
if( $this->me['dep_id']==3 ){ ?>
	<div data-alert data-load="<?=URL?>employees/indenture/<?=$this->item['emp_id']?>"></div>
<?php } ?>
	<div class="profile-main" role="main">
		<div class="uiBoxWhite mal" style="max-width:500px">
			<div class="empty">
				<div class="empty-icon"><i class="icon-file-text-o"></i></div>
				<div class="empty-title">ไม่มีข้อมูลการทำสัญญา</div>
				<?php if( $this->access['edit'] ){ ?>
				<div class="empty-message">
					<div class="mbm">เนื่องจากพนักงานจำเป็นต้องมีหลักฐานการทำสัญญา</div>
					<a href="<?=URL?>employees/indenture/<?=$this->item['emp_id']?>" data-plugins="dialog" class="btn btn-success">สร้างสัญญา</a>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>