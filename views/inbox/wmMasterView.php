<div class="wmMasterView">

<!-- box search -->
<header>
	<form class="form-search" action="search" id="global-nav-search">
		<input class="inputtext search-input" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
	 	<button type="submit" class="btn btn-blue btn-search"><i class="icon-search"></i></button>
	</form>
	
</header>

<div id="leftColMain">
	
	<nav class="nav">
		<ul class="control-list navigation-chat-control clearfix">
			<li><a data-nav-link="person" class="">สมาชิก</a></li>
			<li><a data-nav-link="recent" class="">ล่าสุด</a></li>
			<!-- <li><a><i class="icon-plus"></i></a></li> -->
			<li class="rfloat"><a class="btn-call" title=""><i class="icon-phone"></i>โทร</a></li>
		</ul>
	</nav>
	<!-- <nav>
		<ul class="summary-list clearfix hidden_elem">
			<li><a><span>สมาชิกทั้งหมด</span> <i class="icon-"></i></a></li>
			<li class="rfloat"><span>1-100 of 490</span></li>
		</ul>
	</nav> -->

	<ul class="navigation-chat-content">
		<li class="navigation-chat-list" data-nav-ref="person">
			<ul class="navigation-chat-items list"></ul>
			<div class="navigation-chat-footer">
				<div class="navigation-chat-end"></div>
				<div class="navigation-chat-loading">
					<div class="loader-img loader-spin-wrap"><div class="loader-spin"></div></div>
					<div class="loader-text">กำลังโหลด...</div>
				</div>
			</div>
			<div class="navigation-chat-fail-container"></div>
		</li>
		<li class="navigation-chat-list" data-nav-ref="recent">
			<ul class="navigation-chat-items list"></ul>
			<div class="navigation-chat-footer">
				<div class="navigation-chat-end"></div>
				<div class="navigation-chat-loading">
					<div class="loader-img loader-spin-wrap"><div class="loader-spin"></div></div>
					<div class="loader-text">กำลังโหลด...</div>

				</div>
			</div>
			<div class="navigation-chat-fail-container"></div>
		</li>
	</ul>	
</div>

</div>