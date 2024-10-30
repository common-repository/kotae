<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class KotaeShortcodes {	
	// the main shortcode that publishes the knowledgebase page
	static function kotae() {
		global $wpdb, $post;
		
		if(empty($post->ID)) $permalink = "admin.php?page=kotae";
		else $permalink = get_permalink($post->ID);		
				
		if(!empty($_GET['kotae_topic'])) {
			ob_start();
			KotaeCats :: list_posts($permalink);
			$content = ob_get_clean();
			return $content;
		}
		
		$cats = KotaeCats :: cat_tree();
		
		// match up to X articles to each cat
		foreach($cats as $cnt=>$cat) {
			$posts = $wpdb->get_results("SELECT tP.ID as ID, tP.post_title as post_title
				FROM {$wpdb->posts} tP JOIN {$wpdb->postmeta} tM 
				ON tM.meta_key = 'kotae_cats' AND tM.meta_value LIKE '%|".$cat->id . "|%' AND tM.post_id = tP.ID
				AND tP.post_status = 'publish' AND tP.post_title != ''
				ORDER BY tP.post_title, tP.ID LIMIT ".KOTAE_NUM_ARTICLES);
				
			$cats[$cnt]->posts = $posts;	
		}
		
		ob_start();
		include(KOTAE_PATH . "/views/main.html.php");
		$content = ob_get_clean();
		return $content;
	} // end kotae()
	
	// create search form which will search only within the knowledge base
	static function search($atts) {
		ob_start();
		KotaeSearchController :: form();
		$content = ob_get_clean();
		return $content;	
	}
}