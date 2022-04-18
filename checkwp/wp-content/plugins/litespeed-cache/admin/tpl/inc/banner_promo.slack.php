<?php
if ( ! defined( 'WPINC' ) ) die ;
?>
<div class="litespeed-wrap notice notice-info litespeed-banner-promo-full" id="litespeed-banner-promo-slack">
	<div class="litespeed-banner-promo-logo"></div>

	<div class="litespeed-banner-promo-content">
		<h3 class="litespeed-banner-title"><?php echo __( 'Welcome to LiteSpeed', 'litespeed-cache' ) ; ?></h3>
		<div class="litespeed-banner-description">
			<div class="litespeed-banner-description-padding-right-15">
				<p class="litespeed-banner-desciption-content">
					<?php echo __( 'Want to connect with other LiteSpeed users?', 'litespeed-cache' ) ; ?>
					<?php echo sprintf( __( 'Join the %s community.', 'litespeed-cache' ), '<a href="https://goo.gl/mrKuTw" target="_blank" class="litespeed-banner-promo-slack-textlink">LiteSpeed Slack</a>' ) ; ?>
				</p>
				<p class="litespeed-banner-promo-slack-line2">
					golitespeed.slack.com
				</p>
			</div>
			<div>
				<h3 class="litespeed-banner-button-link">
					<a href="https://goo.gl/mrKuTw" target="_blank">
						<?php echo __( 'Join Us on Slack', 'litespeed-cache' ) ; ?>
					</a>
				</h3>
			</div>
		</div>
	</div>
	<div>
		<?php $dismiss_url = LiteSpeed_Cache_Utility::build_url( LiteSpeed_Cache::ACTION_DISMISS, LiteSpeed_Cache_GUI::TYPE_DISMISS_PROMO, false, null, array( 'promo_tag' => 'banner_promo.slack' ) ) ; ?>
		<span class="screen-reader-text">Dismiss this notice.</span>
		<a href="<?php echo $dismiss_url ; ?>" class="litespeed-notice-dismiss">
			Dismiss
		</a>
	</div>
</div>