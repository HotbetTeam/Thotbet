<div ref="header" class="listpage-header clearfix">
		
	<div ref="actions" class="listpage-actions clearfix">
		<ul class="lfloat">
			<li>บัญชีผู้เข้าใช้ทั้งหมด(<?=$this->data['total']?>)</li>
			<li class="divider"></li>
		</ul>
		<ul class="lfloat" ref="actions">
			<li><a href="<?=URL?>users/form" data-plugins="dialog" class="btn btn-blue"><span class="btn-text">เพิ่มบัญชีใหม่</span></a></li>
		</ul>
		<ul class="lfloat selection hidden_elem" ref="selection">
			<li><span class="count-value">เลือกแล้ว รายการ</span></li>
			<li><a class="btn-icon"><i class="icon-download"></i></a></li>
			<li><a class="btn-icon"><i class="icon-trash"></i></a></li>
		</ul>

		<ul class="rfloat" ref="control">
			<li class="hidden_elem"><form class="form-search" action="/search">
				<input class="search-input" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
				<span class="search-icon">
			 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
				</span>

			</form></li>
			<?php



				$total = $this->data['total'];
				$pager = $this->data['options']['pager'];
				$limit = $this->data['options']['limit'];
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

				if($total > $limit){


				echo '<li>';
				echo '<span class="mhs">'.$first.'-'.$last.' <span class="fcg">จาก</span> '.$total.'</span>';

				echo $pager > 1
		            ? '<a href="'.URL.'manage/foods?pager='.($pager-1).'" class="prev"><i class="icon-angle-left"></i></a>'
		            : '<span class="prev disabled fcg"><i class="icon-angle-left"></i></span>';

		        echo $pager==$length
		            ? '<span class="next disabled fcg"><i class="icon-angle-right"></i></span>'
		            : '<a href="'.URL.'manage/foods?pager='.($pager+1).'" class="prev"><i class="icon-angle-right"></i></a>';
			
		        echo '</li>';

		        }
			?>
		</ul>

	</div>
</div>