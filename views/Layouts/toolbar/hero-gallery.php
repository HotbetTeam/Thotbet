<div id="hero-gallery" class="layout hero-gallery">
	<div class="slider-container">
		<div class="slider-wrapper">
			<ul class="slider-items" style="width:2000px">
				<?php for ($i=0; $i < 2; $i++) { ?>
					<li class="slider-item"><figure>
						<img class="img scaledImageFitWidth" src="<?=IMAGES?>hero-gallery/items/<?=$i?>.jpg" alt="Marvel's Avengers: Age of Ultron"></figure>
					</li>
				<?php }?>
			</ul>
		</div>
		<div class="slider-frame">
			<div class="slider-frame-decor-Left"></div>
			<div class="slider-frame-txt-wrap">
				<div class="slider-frame-inner-right-border">
					<div class="slider-frame-pagination">
						<ul class="slider-pagination">
							<li class="active"><a>1</a></li>
							<?php for ($i=0; $i < 2; $i++) { ?>
							<li><a><?=$i?></a></li>
							<?php }?>
						</ul>
					</div>
				</div>
				<div class="slider-frame-txt-content"></div>
				<div class="slider-frame-decor-right">
					<div class="slider-frame-decors-wrap">
						<div class="slider-frame-maskStop"></div>
						<div class="slider-frame-title">
							<p>Check out the Greatest Gadgets</p>
						</div>
					</div>
					<div class="slider-frame-decor-right-hover"></div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- <div class="slider-frame">
		
		<div class="">
			
			
			<div class="sld_Decor-Right">
				<div class="sld_Decors_Wrap">
					<div class="sld_MaskStop"></div>
					<div class="sld_Next_Title">
						<p>Check out the Greatest Gadgets</p>
					</div>
				</div>
			</div>
		</div>
	</div> -->