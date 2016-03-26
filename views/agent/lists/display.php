<?php require_once 'init.php'; ?>

<div class="listpage has-loading" data-plugins="listpage" data-options="<?= $this->fn->stringify( array_merge( array(
	'url' => $this->getURL,
), $this->results) )?>">

	<!-- header -->
	<?php require 'header.php'; ?>

	<!-- table -->
	<div ref="table" class="listpage-table">
		<div ref="tabletitle"><?php require 'tabletitle.php'; echo $tabletitle; ?></div>
		<div ref="tablelists"><?php //require 'tablelists.php'; ?></div>

		<div class="listpage-table-overlay"></div>
		<div class="listpage-table-empty">
	        <div class="empty-icon"><i class="icon-users"></i></div>
	        <div class="empty-title">ไม่พบข้อมูล</div>
		</div>
	</div>

	<div class="listpage-alert">
		<div class="listpage-loading">
			<div class="listpage-loading-icon loader-spin-wrap"><div class="loader-spin"></div></div>
			<div class="listpage-loading-text">กำลังโหลด...</div> 
		</div>
	</div>
</div>