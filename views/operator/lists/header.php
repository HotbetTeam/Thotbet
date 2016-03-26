<div ref="header" class="listpage-header clearfix">
	<div ref="actions" class="listpage-actions clearfix">
		<ul class="lfloat">
			<li>รายการทั้งหมด (<?=$this->results['total']?>)</li>
			<li class="divider"></li>
		</ul>
		<ul class="lfloat" ref="actions">
			<li><a href="<?=URL?>operator/form" data-plugins="dialog" class="btn btn-blue"><span class="btn-text">เพิ่มใหม่</span></a></li>
		</ul>
		<ul class="lfloat selection hidden_elem" ref="selection">
			<li><span class="count-value"></span></li>
			<li><a class="btn-icon"><i class="icon-download"></i></a></li>
			<li><a class="btn-icon"><i class="icon-trash"></i></a></li>
		</ul>

		<ul class="rfloat" ref="control">
			<li><?php

				$total = $this->results['total'];
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

				echo '<span class="mhs">'.$first.'-'.$last.' <span class="fcg">จาก</span> '.$total.'</span>';

				echo $pager > 1
		            ? '<a href="'.URL.'manage/topics?pager='.($pager-1).'" class="prev"><i class="icon-angle-left"></i></a>'
		            : '<span class="prev disabled"><i class="icon-angle-left"></i></span>';

		        echo $pager==$length
		            ? '<span class="next disabled"><i class="icon-angle-right"></i></span>'
		            : '<a href="'.URL.'manage/topics?pager='.($pager+1).'" class="prev"><i class="icon-angle-right"></i></a>';
			?>
			</li>
			<li class="hidden_elem"><form class="form-search" action="/search">
				<input class="search-input" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
				<span class="search-icon">
			 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
				</span>

			</form></li>
		</ul>

	</div>
</div>