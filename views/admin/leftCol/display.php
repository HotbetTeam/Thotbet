<!-- box search -->
<header>
	<form class="form-search" action="search" id="global-nav-search">
		<input class="inputtext search-input" type="text" id="search-query" placeholder="ค้นหาสมาชิก" name="q" autocomplete="off">
	 	<button type="submit" class="btn btn-blue btn-search"><i class="icon-search"></i></button>
	</form>
</header>

<div id="leftColMain">
	
	<nav>
		<ul class="control-list clearfix">
			<li><label class="checkbox">
				<input type="checkbox">
				<span></span>
			</label></li>
			<!-- <li><a><i class="icon-plus"></i></a></li> -->
			<li class="rfloat"><a title="เพิ่มสมาชิกใหม่" data-plugins="dialog" href="<?=URL?>users/form"><i class="icon-plus"></i></a></li>
		</ul>
	</nav>
	<nav>
		<ul class="summary-list clearfix">
			<li><a><span>สมาชิกทั้งหมด</span> <i class="icon-"></i></a></li>
			<li class="rfloat"><span>1-100 of 490</span></li>
		</ul>
	</nav>
	<section class="">
		<div>
			<ul class="list" id="list" role="list">

				<li class="clearfix active">
					<a href="">
						<div class="clearfix pvs">
							<!-- <div class="avatar mrm lfloat">
								<img src="https://scontent.fbkk5-1.fna.fbcdn.net/hprofile-prn2/v/t1.0-1/c0.16.50.50/p50x50/537947_397331930286847_990514364_n.jpg?oh=e7478dcec8f5711699ca9379e1c6dd17&amp;oe=573FCC2C" width="50" height="50" alt="" class="img">
							</div> -->
							<div class="">
								<div class="name fwb"><span>นาย active</span></div>
								<div class="text clearfix">
									<span>ล่าสุด</span>
								</div>
							</div>
						</div>
					</a>
				</li>
				<?php for ($i=0; $i < 5; $i++) { ?>
					
				<li class="clearfix">
					<a href="">
						<div class="clearfix pvs">
							<!-- <div class="avatar mrm lfloat">
								<img src="https://scontent.fbkk5-1.fna.fbcdn.net/hprofile-prn2/v/t1.0-1/c0.16.50.50/p50x50/537947_397331930286847_990514364_n.jpg?oh=e7478dcec8f5711699ca9379e1c6dd17&amp;oe=573FCC2C" width="50" height="50" alt="" class="img">
							</div> -->
							<div class="">
								<div class="name fwb"><span>นาย วิทย์</span></div>
								<div class="text clearfix">
									<span>ล่าสุด</span>
								</div>
							</div>
						</div>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</section>


</div>