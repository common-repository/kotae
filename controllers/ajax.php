<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
// the ajax dispatcher
function kotae_ajax() {
	global $wpdb, $user_ID;	
	
	switch($_POST['do']) {
		case 'add_category':
			try {
				$id = KotaeCats :: add($_POST);
				if($id) echo 'OK|'.$id;
			}
			catch(Exception $e) { 
				echo $e->getMessage();
			}			
		break;
		
		case 'store_rating':
			$rating_login = get_option('kotae_rating_login');
			if($rating_login and !is_user_logged_in()) return __('Login to rate this article', 'kotae');
			
			if($user_ID) $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".KOTAE_RATINGS." WHERE post_id=%d AND user_id=%d", intval($_POST['post_id']), $user_ID));
			else $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".KOTAE_RATINGS." WHERE post_id=%d AND ip=%s AND user_id=0", intval($_POST['post_id']), $_SERVER['REMOTE_ADDR']));
			
			if($exists) {
				$wpdb->query($wpdb->prepare("UPDATE ".KOTAE_RATINGS." SET
					rating=%d WHERE id=%d", intval($_POST['rating']), $exists));
			} 
			else {
				$wpdb->query( $wpdb->prepare("INSERT INTO ".KOTAE_RATINGS." SET
					post_id=%d, user_id=%d, ip=%s, rating=%d", intval($_POST['post_id']), $user_ID, $_SERVER['REMOTE_ADDR'], intval($_POST['rating'])));
			}	
		break;
	}
	exit;
}