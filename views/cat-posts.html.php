<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<p><a href="<?php echo empty($post->ID) ? 'admin.php?page=kotae' : get_permalink($post->ID);?>"><?php _e('Back to the knowledge base', 'kotae');?></a></p>

<?php if(count($subs)):?>
	<h3><?php _e('Topics:', 'kotae');?></h3>
	<ul class="kotae_subs">
	<?php foreach($subs as $sub):
		$params = array('kotae_topic' => $sub->id);
		$target_url = add_query_arg( $params, $permalink );?>
		<li><a href="<?php echo $target_url?>"><?php echo stripslashes($sub->name);?></a></li>
	<?php endforeach;?>
	</ul>
<?php endif;?>

<?php foreach($posts as $p):
	$excerpt = empty($p->post_excerpt) ? wp_trim_words ( strip_shortcodes( $p->post_content, 55 ) ) : $p->post_excerpt;?>
	<h2><?php echo apply_filters('the_content', stripslashes($p->post_title));?></h2>
	
	<div class="kotae-article">
		<?php echo apply_filters('the_content', $excerpt);?>
		
		<p><a href="<?php echo get_permalink($p->ID);?>"><?php _e('Read full article', 'kotae');?></a></p>
	</div>
<?php endforeach;?>