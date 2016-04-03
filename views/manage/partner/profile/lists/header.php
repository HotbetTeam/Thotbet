<div ref="header" class="listpage-header clearfix">
	<div ref="actions" class="listpage-actions clearfix">
		<ul class="lfloat">
			<li><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>

			<li>สมาชิกทั้งหมดของ Partner: <span class="fwb"><?=$this->item['partner_name']?></span></li>
			<!-- <li class="divider"></li> -->
		</ul>
		<!-- <ul class="lfloat" ref="actions">
			<li><a href="<?=URL?>member/add" data-plugins="dialog" class="btn btn-primary"><i class="icon-plus mrs"></i><span class="btn-text">เพิ่ม</span></a></li>
		</ul> -->
		<ul class="lfloat selection hidden_elem" ref="selection">
			<li><span class="count-value"></span></li>
			<li><a class="btn-icon"><i class="icon-download"></i></a></li>
			<li><a class="btn-icon"><i class="icon-trash"></i></a></li>
		</ul>

		<ul class="rfloat" ref="control">		

			<li><form class="form-search" action="/search">
				<input class="search-input inputtext" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
				<span class="search-icon">
			 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
				</span>

			</form></li>

			<li id="more-link"></li>
		</ul>

	</div>
</div>