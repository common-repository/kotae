<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// add meta boxes and handles some of the meta controls on WP posts
class KotaePosts {
	// adds the meta box with content access settings
	static function meta_box() {
		
		add_meta_box("kotae_cats", __("Kotae Settings", 'kotae'), 
							array(__CLASS__, "print_meta_box"), '', 'side', 'high');		
													
	}
	
	// print the meta box
	static function print_meta_box($post) {
		global $wpdb;

		// get current Kotae categories		
		$cats = KotaeCats :: cat_tree();
		
		$kotae_cats = get_post_meta($post->ID, 'kotae_cats', true);

		// if rating is used calculate averafe
		$ratings = get_option('kotae_ratings');   
		$rating_text = '';
		if(!empty($ratings)) {
			if($ratings == '5stars') {
				$avg_rating = $wpdb->get_var($wpdb->prepare("SELECT AVG(rating) FROM ".KOTAE_RATINGS." WHERE post_id=%d", $post->ID));
				$num_ratings = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".KOTAE_RATINGS." WHERE post_id=%d", $post->ID));
				
				if($num_ratings) $rating_text = sprintf(__('Article rating: %s (from %d ratings)', 'kotae'), round($avg_rating, 2), $num_ratings);
			}
			else {
				// thumbs up/down
				$num_up = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".KOTAE_RATINGS." WHERE post_id=%d AND rating=5", $post->ID));
				$num_down = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".KOTAE_RATINGS." WHERE post_id=%d AND rating=1", $post->ID));				
				$num_ratings = $num_up + $num_down;
				
				if($num_ratings) {
					$percent_up = round(100 * $num_up / $num_ratings);
					$percent_down = 100 - $percent_up;
					
					$rating_text = sprintf(__('Article rating: %d%% thumbs up / %d%% thumbs down (from  %d ratings)', 'kotae'), $percent_up, $percent_down, $num_ratings);
				}
			}
		} // end ratings text
		
		wp_nonce_field( plugin_basename( __FILE__ ), 'kotae_noncemeta' );	
		include(KOTAE_PATH."/views/meta-box.html.php");
	}
	
	static function save_meta($post_id) {		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  return;				
	  	if ( empty($_POST['kotae_noncemeta']) or !wp_verify_nonce( $_POST['kotae_noncemeta'], plugin_basename( __FILE__ ) ) ) return;  	  		
	  	if ( !current_user_can( 'edit_post', $post_id ) ) return;
	   
	   $_POST['kotae_cats'] = array_filter($_POST['kotae_cats'], 'intval');	  
	  	$kotae_cats = empty($_POST['kotae_cats']) ? '' : '|' . implode('|', $_POST['kotae_cats']).'|';
	  	
	  	update_post_meta($post_id, 'kotae_cats', $kotae_cats);
	}
	
	// filter post results so tutorials are always 
	// displayed properly, even in search
	static function post_results($posts) {
		foreach($posts as $post) {
			$kotae_cats = get_post_meta($post->ID, 'kotae_cats', true);
			if(!empty($kotae_cats) and $kotae_cats != '||') {
				self::preprocess_article($post);
			}
		}
		return $posts;
	}	
	
	// preprocess the kotae article accordinly to a template defined in admin
	// Templates are NYI but we can use the concept from the Daskal plugin
	// For now just add the rating widget
	static function preprocess_article(&$post) {
		global $wpdb;
		
		$post_content = $post->post_content;	
		$rating = get_option('kotae_ratings');		
				
		// prepare and replace rating widget
		if(!empty($rating)) {
			
			$_rating = new KotaeRating();
			$widget = $_rating->get_widget($rating, $post);
			
			$post_content .= $widget;
		}		
		
		$post_content.='<script type="text/javascript">
		jQuery(function(){
			if(Kotae.ajax_url == "") Kotae.ajax_url = "'.admin_url("admin-ajax.php").'";
			if(Kotae.plugin_url == "") Kotae.plugin_url = "'.KOTAE_URL.'";
		});
		</script>';		
				
		$post->post_content = $post_content;		 
	} // end preprocess article
}