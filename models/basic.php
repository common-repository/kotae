<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// main model containing general config and UI functions
class Kotae {
   static function install($update = false) {
   	global $wpdb;	
   	$wpdb -> show_errors();
   	
   	if(!$update) self::init();
   	
	  // categories
   	if($wpdb->get_var("SHOW TABLES LIKE '".KOTAE_CATS."'") != KOTAE_CATS) {        
			$sql = "CREATE TABLE `" . KOTAE_CATS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `name` VARCHAR(255) NOT NULL DEFAULT ''				 		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  if($wpdb->get_var("SHOW TABLES LIKE '".KOTAE_RATINGS."'") != KOTAE_RATINGS) {        
			$sql = "CREATE TABLE `" . KOTAE_RATINGS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `post_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `ip` VARCHAR(25) NOT NULL DEFAULT '',
				  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 1
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	   }
	  
	  kotae_add_db_fields(array(
	  	   array("name" => 'sort_order', "type" => "SMALLINT UNSIGNED NOT NULL DEFAULT 0"),
	  	   array("name" => 'parent_id', "type" => "INT UNSIGNED NOT NULL DEFAULT 0"),
 	  ), KOTAE_CATS);
	  	  	  
	  update_option('kotae_version', 0.16);
	  // exit;
   }
   
   // main menu
   static function menu() {
		$manage_caps = current_user_can('manage_options') ? 'manage_options' : 'kotae_manage';   	
   	
   	add_menu_page(__('Kotae Knowledge Base', 'konpro'), __('Kotae Knowledge Base', 'konpro'), $manage_caps, "kotae", 
   		array('Kotae', "options"));
   	add_submenu_page('kotae', __('Manage Categories', 'konpro'), __('Manage Categories', 'konpro'), $manage_caps, "kotae_cats", 
   		array('KotaeCats', "manage"));	
   
	}
	
	// CSS and JS
	static function scripts() {
		wp_enqueue_script('jquery');
		
		// kotae's own Javascript
		wp_register_script(
				'kotae-common',
				KOTAE_URL.'js/main.js',
				false,
				'0.1.2',
				false
		);
		wp_enqueue_script("kotae-common");
		
		$ratings = get_option('kotae_ratings');   
		if($ratings == '5stars') {
			wp_enqueue_script('kotae-star-rating',
			KOTAE_URL.'js/star-rating.js',
			array(),
			'0.4');
		}
		
		 wp_enqueue_style(
		'kotae-style',
		plugins_url().'/kotae/css/main.css',
		array(),
		'0.8');
		
		$translation_array = array(
			'enter_category_name' => __('Please provide category name', 'kotae'),
		);	
		wp_localize_script( 'kotae-common', 'kotae_i18n', $translation_array );	
	}
	
	// admin-only CSS
	static function admin_css() {
	//  wp_register_style( 'kotae-admin-css', KONPRO_URL.'css/admin.css?v=1');
	 // wp_enqueue_style( 'kotae-admin-css' );
	}
	
	// initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'kotae', false, KOTAE_RELATIVE_PATH."/languages/" );
		if (!session_id()) @session_start();
		
		// define table names 
		define('KOTAE_CATS', $wpdb->prefix.'kotae_cats');
		define('KOTAE_RATINGS', $wpdb->prefix.'kotae_ratings');
		
		// default settings
		$num_columns = get_option('kotae_num_columns');
		if(intval($num_columns) < 1) {
			$num_columns = 3;
			update_option('kotae_num_columns', $num_columns);
		}
		define('KOTAE_NUM_COLUMNS', $num_columns);
		$num_articles = get_option('kotae_num_articles');
		if(intval($num_articles) < 1) {
			$num_articles = 5;
			update_option('kotae_num_articles', $num_articles);
		}
		define('KOTAE_NUM_ARTICLES', intval($num_articles));
		
		define( 'KOTAE_VERSION', get_option('kotae_version'));
		
		// meta boxes
		add_action( 'add_meta_boxes', array('KotaePosts', 'meta_box') );
		add_action( 'save_post', array('KotaePosts', 'save_meta') );
		
		// shortcodes
		add_shortcode('kotae', array('KotaeShortcodes', 'kotae'));
		add_shortcode('kotae-search', array('KotaeShortcodes', 'search'));
		
		// run activate
		$version = get_option('kotae_version');
		if(empty($version) or $version < 0.16) self :: install(true);
	}
	

			
	// manage general options
	static function options() {
		if(!empty($_POST['ok']) and check_admin_referer('kotae_settings')) {
			update_option('kotae_num_columns', intval($_POST['num_columns']));
			update_option('kotae_num_articles', intval($_POST['num_articles']));
			if(!in_array($_POST['kotae_ratings'], array('', 'none', '5stars', 'hands'))) $_POST['kotae_ratings'] = 'hands';
			update_option('kotae_ratings', $_POST['kotae_ratings']);
		}		
		
		$ratings = get_option('kotae_ratings');		
		// echo $ratings;
		require(KOTAE_PATH."/views/options.html.php");
	}	
	
	static function help() {
		require(KOTAE_PATH."/views/help.html.php");
	}	
}