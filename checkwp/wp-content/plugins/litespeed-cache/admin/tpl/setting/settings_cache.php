<?php
if ( ! defined( 'WPINC' ) ) die ;
?>

<h3 class="litespeed-title-short">
	<?php echo __('Cache Control Settings', 'litespeed-cache'); ?>
	<?php $this->learn_more( 'https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:configuration:cache', false, 'litespeed-learn-more' ) ; ?>
</h3>

<?php $this->cache_disabled_warning() ; ?>

<table><tbody>
	<tr>
		<th><?php echo __( 'Cache Logged-in Users', 'litespeed-cache' ) ; ?></th>
		<td>
			<?php $this->build_switch( LiteSpeed_Cache_Config::OPID_CACHE_PRIV ) ; ?>
			<div class="litespeed-desc">
				<?php echo sprintf( __( 'Privately cache frontend pages for logged-in users. (LSWS %s required)', 'litespeed-cache' ), 'v5.2.1+' ) ; ?>
			</div>
		</td>
	</tr>

	<tr>
		<th><?php echo __( 'Cache Commenters', 'litespeed-cache' ) ; ?></th>
		<td>
			<?php $this->build_switch( LiteSpeed_Cache_Config::OPID_CACHE_COMMENTER ) ; ?>
			<div class="litespeed-desc">
				<?php echo sprintf( __( 'Privately cache commenters that have pending comments. Disabling this option will serve non-cacheable pages to commenters. (LSWS %s required)', 'litespeed-cache' ), 'v5.2.1+' ) ; ?>
			</div>
		</td>
	</tr>

	<tr>
		<th><?php echo __( 'Cache REST API', 'litespeed-cache' ) ; ?></th>
		<td>
			<?php $this->build_switch( LiteSpeed_Cache_Config::OPID_CACHE_REST ) ; ?>
			<div class="litespeed-desc">
				<?php echo __( 'Cache requests made by WordPress REST API calls.', 'litespeed-cache' ) ; ?>
			</div>
		</td>
	</tr>

	<tr>
		<th><?php echo __( 'Cache Login Page', 'litespeed-cache' ) ; ?></th>
		<td>
			<?php $this->build_switch( LiteSpeed_Cache_Config::OPID_CACHE_PAGE_LOGIN ) ; ?>
			<div class="litespeed-desc">
				<?php echo __( 'Disabling this option may negatively affect performance.', 'litespeed-cache' ) ; ?>
			</div>
		</td>
	</tr>

	<?php
		if ( ! is_multisite() ) :
			require LSCWP_DIR . 'admin/tpl/setting/settings_inc.cache_favicon.php' ;
			require LSCWP_DIR . 'admin/tpl/setting/settings_inc.cache_resources.php' ;
			require LSCWP_DIR . 'admin/tpl/setting/settings_inc.cache_mobile.php' ;
		endif ;
	?>

	<tr <?php echo $_hide_in_basic_mode ; ?>>
		<th><?php echo __( 'Private Cached URIs', 'litespeed-cache' ) ; ?></th>
		<td>
			<?php $this->build_textarea2( LiteSpeed_Cache_Config::ITEM_CACHE_URI_PRIV ) ; ?>
			<div class="litespeed-desc">
				<?php echo __('URI Paths containing these strings will NOT be cached as public.', 'litespeed-cache'); ?>
				<?php $this->_uri_usage_example() ; ?>
			</div>
		</td>
	</tr>

	<tr <?php echo $_hide_in_basic_mode ; ?>>
		<th><?php echo __( 'Drop Query String', 'litespeed-cache' ) ; ?></th>
		<td>
			<?php $this->build_textarea2( LiteSpeed_Cache_Config::ITEM_CACHE_DROP_QS, 40 ) ; ?>
			<div class="litespeed-desc">
				<?php echo __('Ignore certain query strings when caching.', 'litespeed-cache'); ?>
				<?php echo sprintf( __( 'For example, to drop parameters beginning with %s, %s can be used here.', 'litespeed-cache' ), '<code>utm</code>', '<code>utm*</code>' ) ; ?>
				<?php $this->learn_more( 'https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:drop_query_string' ) ; ?>

				<br />
				<i>
					<?php echo __('One per line.', 'litespeed-cache'); ?>
				</i>
			</div>
		</td>
	</tr>

</tbody></table>

