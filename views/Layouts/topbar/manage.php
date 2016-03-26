<?php

$navigation = "";
if( !empty($this->nav) ){

	$li = '';
	foreach ($this->nav as $key => $value) {
		$li .= isset($value['url'])
			? '<li><a class="navLink" href="'.$value['url'].'">'.$value['text'].'</a></li>'
			: '<li><span class="navLink">'.$value['text'].'</span></li>';
	}

	$navigation = '<div role="navigation" class="lfloat"><ul class="breadcrumb">'.$li.'</ul></div>';
}

?>
<div id="tobar" class="clearfix">
	<div class="global-nav">

		<h1 id="logo"></h1>
		<!-- <a class="btn navigation-trigger"><i class="icon-bars"></i></a> -->

		<?=$navigation;?>
		<div class="pull-right">
			<div role="search">
				<form class="form-search js-search-form" action="/search" id="global-nav-search">
					<input class="search-input" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
					<span class="search-icon js-search-action">
				 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
					</span>

				</form>
			</div>
			<ul id="pageNav" class="nav">
				<li class="plus"><a><i class="icon-plus"></i></a></li>

					<li class="uiToggle queryNotify" role="notifications">

					<a class="btn-toggle link-notify btn-icon fcn" data-plugins="toggleLink">
						<i class="icon-bell-o"></i>
						<span class="countValue">0</span>
					</a>

					<div class="uiToggleFlyout uiToggleFlyoutRight toggleNotify">
						<ul role="content" class="uiMenu"><li class="notify-header clearfix"><div class="rfloat"></div><div><h3 class="fwb fsl">การแจ้งเตือน</h3></div></li><li class="notify-empty">ไม่มีการแจ้งเตือนใหม่</li><li class="notify-content"><ul role="listsbox"></ul></li><li class="notify-loading">กำลังโหลด...</li></ul>
					</div>

				</li>
			</ul>
		</div>
	</div>
</div>