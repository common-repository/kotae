<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class KotaeSearchController {
	// when Koate search is done we have to search only in posts that have any Kotae categories assigned to them
   static function pre_get_posts($query) {
   	global $wpdb, $user_ID;   	   	
   	if(empty($_GET['kotae_search'])) return $query;
   	
  		add_filter('posts_join', array(__CLASS__, 'filter_join'));
   	return $query;
   } // end pre_get_posts filter
    
    
   // filters the posts based on the drop-down selection AND the user's enrollment
   static function filter_join($join) {
		global $wpdb, $user_ID;
		
		if(empty($_GET['kotae_search'])) return $join;
		
		// later on we may consider a category dropdown and search by specific cats
		// NYI

		$join .= "/* kotae join */ INNER JOIN {$wpdb->postmeta} kotaeMeta ON {$wpdb->posts}.ID = kotaeMeta.post_id 
	   	AND kotaeMeta.meta_key = 'kotae_cats' AND kotaeMeta.meta_value !='' ";
   	// echo $join;
   	return $join;
   } 
   
   // create search form
   static function form() {
   	global $wpdb, $user_ID;
   	
   	if(@file_exists(get_stylesheet_directory().'/kotae/search-form.html.php')) require get_stylesheet_directory().'/kotae/search-form.html.php';
		else require(KOTAE_PATH."/views/search-form.html.php");
	}
}