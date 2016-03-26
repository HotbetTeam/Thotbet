<div class="wmMasterView">

<!-- box search -->
<header>
	<form class="form-search" action="search" id="global-nav-search">
		<input class="inputtext search-input" type="text" id="search-query" placeholder="ค้นหาสมาชิก" name="q" autocomplete="off">
	 	<button type="submit" class="btn btn-blue btn-search"><i class="icon-search"></i></button>
	</form>
</header>

<div id="leftColMain">
	
	<nav class="hidden_elem">
		<ul class="control-list clearfix">
			<!-- <li><label class="checkbox">
				<input type="checkbox">
				<span></span>
			</label></li> -->
			<!-- <li><a><i class="icon-plus"></i></a></li> -->
			<li class="rfloat"><a title="เพิ่มสมาชิกใหม่" data-plugins="dialog" href="<?=URL?>users/form"><i class="icon-plus"></i></a></li>
		</ul>
	</nav>
	<nav>
		<ul class="summary-list clearfix hidden_elem">
			<li><a><span>สมาชิกทั้งหมด</span> <i class="icon-"></i></a></li>
			<li class="rfloat"><span>1-100 of 490</span></li>
		</ul>
	</nav>
	<section class="">
		<div>
			<ul class="list" id="recent" role="recent" data="<?=$this->fn->stringify( $this->recent )?>">

				<?php foreach ($this->recent['lists'] as $key => $item) { ?>
					
				<li class="clearfix" id="recent:<?=$item['c_id']?>">
					<a href="">
						<div class="clearfix pvs" >
							<!-- <div class="avatar mrm lfloat">
								<img src="" width="50" height="50" alt="" class="img">
							</div> -->
							<div class="status-online js-user-online" data-user-id="<?=$item['user_id']?>"><i></i>
								<span class="offline">ออฟไลน์</span>
								<span class="online">ออนไลน์</span>
							</div>

							<div class="clearfix">
								<div class="name">
									<strong><?=$item['name']?></strong> 
									<?php if( !empty($item['phone_number']) ) { ?><span>(<?=$item['phone_number']?>)</span> <?php } ?>
								</div>	
								<?php if( !empty($item['latest']) ){ ?>
									<div class="text clearfix">
										<?=$item['latest']['text']?>
									</div>
								<?php } ?>
							</div>
						</div>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</section>
	
</div>

</div>