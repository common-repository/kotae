<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap">
	<h1><?php _e('Kotae Knowledge Base Settings', 'kotae');?></h1>
	
	<form method="post">
		<div class="inside">
			<p><?php printf(__('Arrange the knowledge base articles in %s columns.', 'kotae'), '<input type="text" name="num_columns" size="2" maxlength="1" value="'.get_option('kotae_num_columns').'">');?></p>
			<p><?php printf(__('Show up to %s articles per topic (then display "More" link).', 'kotae'), '<input type="text" name="num_articles" size="2" maxlength="2" value="'.get_option('kotae_num_articles').'">');?></p>
			<p><?php _e('Rating widget:', 'kotae')?> <select name="kotae_ratings">
			<option value="none" <?php if( empty($ratings) or $ratings == 'none' ) echo 'selected'?>><?php _e('None', 'kotae')?></option>		
			<option value="5stars" <?php if( !empty($ratings) and $ratings == '5stars' ) echo 'selected'?>><?php _e('Five stars widget', 'kotae')?></option>
			<option value="hands" <?php if( !empty($ratings) and $ratings == 'hands' ) echo 'selected'?>><?php _e('Hads up / Hands down widget', 'kotae')?></option>
			</select>
			</p>
			<p><input type="submit" name="ok" value="<?php _e('Save Settings', 'kotae');?>"></p>
		</div>
		<?php wp_nonce_field('kotae_settings');?>
	</form>
	
	<h2><?php _e('Main Knowledge Base Page Preview', 'kotae');?></h2>
	
	<p><?php printf(__('You can publish this page in any post or page using the shortcode %s.', 'kotae'), '<input type="text" value="[kotae]" onclick="this.select();" readonly="readonly" size="8">');?> </p>
	
	<div class="inside">
		<?php echo do_shortcode('[kotae]');?>
	</div>
</div>