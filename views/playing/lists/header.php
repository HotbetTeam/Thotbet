<div ref="header" class="listpage-header clearfix">
	<div ref="actions" class="listpage-actions clearfix">
		<ul class="lfloat">
			<!-- <li class="title"> สมาชิก (<?=$this->results['total']?>) </li> -->
			<!-- <li><a class="btn"><i class="icon-refresh"></i></a></li> -->
			<li>
				<div class="range-selector" ref="rangeSelector">
					<div class="date-selector">
						<?php 

						$year = '';
						for ($i=date('Y'); $i >= PAGE_ANCHOR_DATE; $i--) { 
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

			<li class="divider"></li>
		</ul>
		<ul class="lfloat" ref="actions">
			<li>
				<div class="group-btn uiToggle">
					<a href="<?=URL?>playing/form" data-plugins="dialog" class="btn btn-primary"><i class="icon-plus mrs"></i><span class="btn-text">เพิ่ม</span></a>
					<a class="btn btn-primary btn-toggle" data-plugins="toggleLink" data-options="<?= $this->fn->stringify( array('right' => true ) ) ?>"><i class="icon-angle-down"></i></a>

					<div class="uiToggleFlyout">
						<ul role="content" class="uiMenu">
							<li class="menuItem"><a href="<?=URL?>playing/upload" data-plugins="dialog" class="itemAnchor"><span class="itemLabel"><i class="icon-upload mrs"></i>อัพโหลด</span></a></li>
						</ul>
					</div>
				</div>
				
            </li>
		</ul>
		<ul class="lfloat selection hidden_elem" ref="selection">
			<li><span class="count-value"></span></li>
			<li><a class="btn-icon"><i class="icon-download"></i></a></li>
			<li><a class="btn-icon"><i class="icon-trash"></i></a></li>
		</ul>

		<ul class="rfloat" ref="control">

			<li class="hidden_elem"><form class="form-search" action="/search">
				<input class="search-input inputtext" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
				<span class="search-icon">
			 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
				</span>

			</form></li>

			<li id="more-link"></li>
			<?php

				/*$total = $this->results['total'];
				$pager = $this->results['options']['pager'];
				$limit = $this->results['options']['limit'];
				$url = URL.'foods/';

				$length = floor( $total/$limit );
				if($total%$limit){
					$length++;
				};

				$first = ($limit*$pager)-$limit+1;
				$last = $limit*$pager;

				if( $last>$total ){
					$last = $total;
				}

				// if($total > $limit){


				echo '<li>';
				echo '<span class="mhs">'.$first.'-'.$last.' <span class="fcg">จาก</span> '.$total.'</span>';

				echo $pager > 1
		            ? '<a href="'.URL.'manage/foods?pager='.($pager-1).'" class="prev"><i class="icon-angle-left"></i></a>'
		            : '<span class="prev disabled fcg"><i class="icon-angle-left"></i></span>';

		        echo $pager==$length
		            ? '<span class="next disabled fcg"><i class="icon-angle-right"></i></span>'
		            : '<a href="'.URL.'manage/foods?pager='.($pager+1).'" class="prev"><i class="icon-angle-right"></i></a>';
			
		        echo '</li>';*/

		        // }
			?>
		</ul>

	</div>
</div>