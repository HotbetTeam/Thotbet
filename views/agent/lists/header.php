<div ref="header" class="listpage-header clearfix">
	<div ref="actions" class="listpage-actions clearfix">
		<ul class="lfloat">
			<li><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>
			<li class="selector"><?php
				// set Actions

				$option = '';
				$statusVerifyCounts = 0;
				$a_title = "สมาชิกทั้งหมด ({$this->results['total']})";
		        foreach ($this->statusCounts as $key => $value) {

		        	$statusVerifyCounts += $key=='verify' ? $value['total']:0;

		        	$sel = $this->status==$key ? ' selected="1"':'';

		        	if( $this->status==$key ){
		        		$a_title = $value['text'].($value['total']>0?" ({$value['total']})":"");
		        	}

		        	/*$option .= '<option'.$sel.' value="'.$key.'">'.$value['text'].(
		        		$value['total']>0 ? " ({$value['total']})":""
		        	).'</option>';*/
		        }

		        $a = $this->ui->toggle()
				->title(array(
					'text'=> $a_title,
					'class'=>"btn-txt",
					'ricon' => 'icon-angle-down',
				))
				->position( 'right' );

				foreach ($this->statusCounts as $key => $value) {

					$text = $value['text'].($value['total']>0?" ({$value['total']})":"");

					$a->option( $text )->link(URL."member/?status={$key}");
				} 

		        // echo '<select name="status" _ref="selector">'.$option.'</select>';
				echo $a->getPluginJquey();
			?></li>

			<li></li>
		
			<!-- <li>
				<div class="range-selector" ref="rangeSelector">
					<div class="date-selector">
						<select name="range_selector" class="inputtext">
							<option value="last24hours">24 ชั่วโมงล่าสุด</option>
							<option value="last7days">7 วันล่าสุด</option>
							<option value="last30days" selected="1">30 วันล่าสุด</option>
							<option value="custom">กำหนดเอง</option>
						</select>
					</div>
					<div class="date-start"></div>
					<div class="date-to-text">ถึง</div>
					<div class="date-end"></div>
				</div>
			</li> -->

			<li class="divider"></li>
		</ul>
		<ul class="lfloat" ref="actions">
			<li><a href="<?=URL?>member/add" data-plugins="dialog" class="btn btn-primary"><i class="icon-plus mrs"></i><span class="btn-text">เพิ่ม</span></a></li>
		</ul>
		<ul class="lfloat selection hidden_elem" ref="selection">
			<li><span class="count-value"></span></li>
			<li><a class="btn-icon"><i class="icon-download"></i></a></li>
			<li><a class="btn-icon"><i class="icon-trash"></i></a></li>
		</ul>

		<ul class="rfloat" ref="control">

			<?php if( $statusVerifyCounts>0 && $this->status!='verify' ){ ?>
			<li><a href="<?=URL?>member/?status=verify" class="btn-icon fcn hasCount"><i class="icon-user-plus"></i><span class="countValue"><?=$statusVerifyCounts?></span></a></li>
			<?php } ?>			

			<li class="hidden_elem"><form class="form-search" action="/search">
				<input class="search-input inputtext" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
				<span class="search-icon">
			 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
				</span>

			</form></li>

			<li id="more-link"></li>
		</ul>

	</div>
</div>