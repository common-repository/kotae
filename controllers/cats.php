<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// manage Kotae categories 
class KotaeCats {
	// manage knowledge base categories
	static function manage() {
		global $wpdb;		
		
		$parent_id_param = empty($_GET['parent_id']) ? "" : "&parent_id=".$_GET['parent_id'];
		
		if(!empty($_POST['add']) and check_admin_referer('kotae_cats')) {
			self :: add($_POST);
			kotae_redirect("admin.php?page=kotae_cats" . $parent_id_param);
		}
		
		if(!empty($_POST['save']) and check_admin_referer('kotae_cats')) {
			self :: save($_POST, $_POST['id']);
			kotae_redirect("admin.php?page=kotae_cats" . $parent_id_param);
		}
		
		if(!empty($_POST['del']) and check_admin_referer('kotae_cats')) {
			self :: delete($_POST['id']);
			kotae_redirect("admin.php?page=kotae_cats" . $parent_id_param);
		}
		
		if(!empty($_GET['parent_id'])) {
			$parent = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KOTAE_CATS." WHERE id=%d", intval($_GET['parent_id'])));
		}
		
		// select cats 
		$parent_id = empty($_GET['parent_id']) ? 0 : intval($_GET['parent_id']);
		$cats = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".KOTAE_CATS." WHERE parent_id=%d 
			ORDER BY sort_order, name", $parent_id));
		
		include(KOTAE_PATH . '/views/cats.html.php');
	}
	
	static function add($vars) {
		global $wpdb;
		
		$vars['name'] = sanitize_text_field($vars['name']);
		
		$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".KOTAE_CATS." 
			WHERE name=%s AND parent_id=%d", $vars['name'], $vars['parent_id']));
		if($exists) throw new Exception(__('Category with this name already exists.', 'kotae'));
		
		// add with max sort_order
		$sort_order = $wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".KOTAE_CATS. " WHERE parent_id=%d", $vars['parent_id']));
		$sort_order++;
		
		$wpdb->query($wpdb->prepare("INSERT INTO ".KOTAE_CATS." SET
			name=%s, sort_order=%d, parent_id=%d", $vars['name'], $sort_order, $vars['parent_id']));
		return $wpdb->insert_id;	
	}
	
	static function save($vars, $id) {
		global $wpdb;
		
		$vars['name'] = sanitize_text_field($vars['name']);
		$id = intval($id);
		
		$parent_id = $wpdb->get_var($wpdb->prepare("SELECT parent_id FROM ".KOTAE_CATS." WHERE id=%d", $id));
		
		$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".KOTAE_CATS." 
			WHERE name=%s AND id!=%d AND parent_id!=%d", $vars['name'], $id, $parent_id));
		if($exists) throw new Exception(__('Category with this name already exists.', 'kotae'));
		
		$wpdb->query($wpdb->prepare("UPDATE ".KOTAE_CATS." SET
			name=%s, sort_order=%d WHERE id=%d", $vars['name'], $vars['sort_order'], $id));
			
		return true;	
	}
	
	static function delete($id) {
		global $wpdb;
		$id = intval($id);		
		
		$wpdb->query($wpdb->prepare("DELETE FROM " . KOTAE_CATS . " WHERE id=%d", $id));
		
		return true;
	}
	
	// list the posts on the front-end
	static function list_posts($permalink) {
		global $wpdb, $post;
		
		// select category
		$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . KOTAE_CATS . " WHERE id=%d ", intval($_GET['kotae_topic'])));
		if(empty($cat->id)) return __('Category not found.', 'kotae');
		if(empty($post)) $post = (object)array();
		$post->post_title = stripslashes($cat->name);		
		
		// select posts
		$posts = $wpdb->get_results("SELECT tP.ID as ID, tP.post_title as post_title, tP.post_excerpt as post_excerpt, tP.post_content as post_content
				FROM {$wpdb->posts} tP JOIN {$wpdb->postmeta} tM 
				ON tM.meta_key = 'kotae_cats' AND tM.meta_value LIKE '%|".$cat->id . "|%' AND tM.post_id = tP.ID
				AND tP.post_status = 'publish' AND tP.post_title != ''
				ORDER BY tP.post_title, tP.ID");
				
		// sub categories?
		$subs = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . KOTAE_CATS. " WHERE parent_id=%d", $cat->id));		
		
		include(KOTAE_PATH . "/views/cat-posts.html.php");		
	}
	
	// returns the "tree" of categories and subcategories
	static function cat_tree() {
		global $wpdb;
		$cats = $wpdb->get_results("SELECT * FROM ".KOTAE_CATS." WHERE parent_id=0 ORDER BY sort_order, name");
		$subs = $wpdb->get_results("SELECT * FROM ".KOTAE_CATS." WHERE parent_id!=0 ORDER BY sort_order, name");
		
		foreach($cats as $cnt=>$cat) {
			$cat_subs = array();
			foreach($subs as $sub) {
				if($sub->parent_id == $cat->id) $cat_subs[] = $sub;
			}
			
			$cats[$cnt]->subs = $cat_subs;
		}
		
		return $cats;
	}
}