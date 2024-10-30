<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="kotae-search">
	<form class="kotae-search-form" method="get" action="<?php echo home_url();?>">		
		<p>
			<input name="s" type="search" class="search-field" placeholder="<?php _e('Search...', 'kotae');?>" value="<?php echo empty($_GET['s']) ? '' : $_GET['s']?>">
			
			<input type="submit" class="search-submit" value="<?php _e('Search', 'kotae');?>" />
		</p>
		<input type="hidden" name="kotae_search" value="1">
	</form>
</div>