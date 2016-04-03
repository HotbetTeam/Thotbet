<div ref="header" class="listpage-header clearfix">
	<div ref="actions" class="listpage-actions clearfix">
		<ul class="lfloat">
			<li><a class="btn" href="<?=URL?>partner/member"><i class="icon-long-arrow-left"></i></a></li>
			<li class="divider"></li>
			<li class="title fwb fsxl"> ข้อมูลการเล่นเกม </li>
			<li><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>
			<li>
				<div class="range-selector" ref="rangeSelector">
					<div class="date-selector">
						<?php 

						$year = '';
						for ($i=date('Y'); $i >= date('Y', strtotime($this->item['created'])) ; $i--) { 
							$year .= '<option value="'.$i.'">'.$i.'</option>';
						}
						?>
						<select name="range_selector" class="inputtext">
							<option value="custom" disabled="1">กำหนดเอง</option>
							<optgroup label="กำหนดการ:">
								<option value="last24hours">24 ชั่วโมงล่าสุด</option>
								<option value="last7days">7 วันล่าสุด</option>
								<option value="last30days" selected="1">30 วันล่าสุด</option>
							</optgroup>
							<optgroup label="ปี:"><?=$year?></optgroup>
						</select>
					</div>
					<div class="date-start"></div>
					<div class="date-to-text">ถึง</div>
					<div class="date-end"></div>
				</div>
			</li>

		</ul>

		<ul class="rfloat" ref="control">
			<li id="more-link"></li>
		</ul>

	</div>
</div>