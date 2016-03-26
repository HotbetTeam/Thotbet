<?php

if( empty($this->item['mark_working']) && in_array($this->me['dep_id'], array(1,3)) ){ ?>
<div class="uiBoxYellow bottomborder pam">
	ยังไม่มีการกำหนดจุดทำงานให้กับพนักงาน<a data-plugins="dialog" href="<?=URL?>employees/mark_working/<?=$this->item['emp_id']?>" class="btn mlm btn-success">กำหนดจุดทำงาน</a>
</div>

<?php } ?>

<?php if( in_array($this->me['dep_id'], array(1,2)) && $this->item['display']=='enabled' ){ ?>
<div class="toolbarControls" role="toolbarControls">
	<div class="clearfix">
		<ul class="lfloat">

			<li><h2 role="ControlTitle"></h2></li>

			<li><label class="checkbox"><input id="checkboxes" type="checkbox" name="shift-morning" /><span class="mls">06.00-18.00 น.ทุกวัน</span></label></li>
			<li><label class="checkbox"><input id="checkboxes" type="checkbox" name="shift-night"  /><span class="mls">18.00-06.00 น.ทุกวัน</span></label></li>
		</ul>
		<ul class="rfloat">
			<li><a role="addTasks" class="btn btn-primary">เรียบร้อย</a></li>
			<li><a role="toggleSchedule" class="btn-icon"><i class="icon-remove"></i></a></li>
		</ul>
	</div>

</div>
<?php } ?>


<div class="profile-nav clearfix insights-controls">

<?php
$actions = $this->ui->toggle()
	->title(array(
		'text'=>'ประวัติการทำงาน', //ellipsis-h
		'class'=>"btn btn-white",
		'ricon' => 'icon-angle-down',
	))
	->position( 'right' )
	->option('ประวัติการทำงาน')
	->option('คำนวณเงินเดือน');

?>

		<div class="lfloat">
			<div class="groups-btn hidden_elem">
				<?=$actions->html()?>
			</div>

			<div class="groups-btn">
			
				<select class="hidden_elem" role="year"></select>
				<select class="hidden_elem" role="month"></select>
				
		    	<!-- <a class="btn btn-white"><span class="btn-text">ปี: 2558</span><i class="mls icon-angle-down img"></i></a> -->
				<!-- <a class="btn btn-white"><span class="btn-text">เดือน: กรกฏาคม</span><i class="mls icon-angle-down img"></i></a> -->
		    </div>
	    </div>

		<div class="rfloat clearfix">
				
			<?php if( !empty($this->item['mark_working']) && in_array($this->me['dep_id'], array(1,2)) && $this->item['display']=='enabled' ){ ?>
			<div class="groups-btn">
				<a role="schedule" class="btn btn-white">กำหนดการทำงาน</a>
				<a role="Salary_Calculator" ajaxify="<?=URL?>employees/salary/<?=$this->item['emp_id']?>" class="btn btn-white">คำนวณเงินเดือน</a>
			</div>
			<?php } ?>
			<!-- <div class="insights-views groups-btn">
				<a data-view="chart" class="btn btn-white active"><i class="icon-calendar"></i></a>
				<a data-view="table" class="btn btn-white disabled"><i class="icon-list"></i></a>
		    </div> -->
<?php
if( $this->item['display']=='enabled' ){


$actions = $this->ui->toggle()
	->title(array(
		'text'=>'<i class="icon-cog"></i>', //ellipsis-h
		'class'=>"btn-icon",
	))
	->position( 'right' );

$actions->option('เบิกอุปกรณ์')
		->link(URL.'products/order/?emp_id='.$this->item['emp_id'])
		->attr('data-plugins', 'dialog');

if( in_array($this->me['dep_id'], array(1,2)) ){
	$actions->option('เบิกเงินล่วงหน้า')
		->link(URL.'employees/cash_advance/'.$this->item['emp_id'])
		->attr('data-plugins', 'dialog');
}

if( in_array($this->me['dep_id'], array(1,3)) ){
	$actions->option('เปลี่ยนจุดทำงาน')
		->link(URL.'employees/mark_working/'.$this->item['emp_id'])
		->attr('data-plugins', 'dialog');
}

?>	
		<div class="rfloat mlm"><?=$actions->getPluginJquey()?></div>
<?php } ?>    
		</div>
</div>