<?php
/*
Plugin Name: Kotae
Plugin URI: http://namaste-lms.org/kotae.php
Description: A super easy knowledge base plugin. Lets you arrange your existing posts in Knowledge base page
Author: Kiboko Labs
Version: 0.8.4
Author URI: http://kibokolabs.com
License: GPLv2 or later
Text-domain: kotae
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'KOTAE_PATH', dirname( __FILE__ ) );
define( 'KOTAE_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'KOTAE_URL', plugin_dir_url( __FILE__ ));

// require controllers and models
require_once(KOTAE_PATH.'/models/basic.php');
require_once(KOTAE_PATH.'/controllers/posts.php');
require_once(KOTAE_PATH.'/controllers/ajax.php');
require_once(KOTAE_PATH.'/controllers/cats.php');
require_once(KOTAE_PATH.'/controllers/shortcodes.php');
require_once(KOTAE_PATH.'/helpers/htmlhelper.php');
require_once(KOTAE_PATH.'/controllers/search.php');
require_once(KOTAE_PATH.'/models/rating.php');

add_action('init', array("Kotae", "init"));

register_activation_hook(__FILE__, array("Kotae", "install"));
add_action('admin_menu', array("Kotae", "menu"));
add_action('admin_enqueue_scripts', array("Kotae", "scripts"));
add_action('admin_enqueue_scripts', array("Kotae", "admin_css"));
add_filter( 'posts_results', array("KotaePosts", "post_results") );

// show the things on the front-end
add_action( 'wp_enqueue_scripts', array("Kotae", "scripts"));

// other actions
add_action('wp_ajax_kotae_ajax', 'kotae_ajax');
add_action('wp_ajax_nopriv_kotae_ajax', 'kotae_ajax');
add_action('pre_get_posts', array('KotaeSearchController', 'pre_get_posts'));