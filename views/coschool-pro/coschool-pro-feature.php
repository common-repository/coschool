<?php 
use Codexpert\CoSchool\Helper;

?>
<div class="wrap">

	<div class="cs-pro-main-div" style="background-image: url( <?php echo COSCHOOL_ASSET . '/img/bg.jpg' ?> );">
		<div class="cs-pro-wrap-main">
			<h2 class="cs-pro-title"><?php esc_html_e( 'Expand your eLearning Platform with the ', 'coschool' ); ?> <span class="cs-pro-title-span">Premium</span> Features of CoSchool </h2>
				
			<div class="cs-pro-main-content">
				<?php 
					$addons = coscholl_pro_add_ons();
					foreach ( $addons as $key => $value ) {					
				?>			
				<div class="cs-pro-card-box">				
					<div class="cs-pro-card-top">
						<img src="<?php echo COSCHOOL_ASSET . '/img/' .$value['image'] ?>">
					</div>
					<div class="cs-pro-card-buttom">
						<h3> <?php echo $value['title'] ?> </h3>
						<p> <?php echo $value['description'] ?> </p>
					</div>				
				</div>
				<?php 
				}			 
				?>			
			</div>		
			<div class="cs-pro-footer-button">
				<a href="https://codexpert.io/coschool/pricing/" target="_blank" type="button">Upgrade To Pro <span> <img src="<?php echo COSCHOOL_ASSET . '/img/Pro.png' ?>" class="cs-white-crown"> </span>
				</a>
			</div>
		</div>
	</div>
</div>