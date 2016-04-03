<div class="settings-content-title">Banner</div>

<div>
	<ul class="setting-banner-list">
		<?php foreach ($this->banners as $key => $value) { ?>
		<li data-plugins="save_banner" data-options="<?=$this->fn->stringify( array(
			'width' => $value['banner_width'],
			'height' => $value['banner_height'],
			'id' => $value['banner_id'],
			'url' => URL.'banner/upload'
		) )?>">
			<div class="image-cover" style="width:<?=$value['banner_width']?>px;height:<?=$value['banner_height']?>px;"><div class="loader"></div><div class="preview"><?php 

			if( !empty($value['banner_image_url']) ){
				echo '<img src="'.IMAGES.'banner/'.$value['banner_image_url'].'" />';
			}

			?></div><div class="dropzone" style="width:<?=$value['banner_width']?>px;height:<?=$value['banner_height']?>px;"><div class="dropzone-text"><div class="dropzone-icon"><i class="icon-image"></i></div><div class="dropzone-title mtm"><?=$value['banner_width']?> x <?=$value['banner_height']?></div><div class="dropzone-des fsm mts"></div></div><div class="media-upload"><input type="file" accept="image/*" name="file1"></div></div></div>
		</li>
		<?php } ?>
	</ul>
</div>