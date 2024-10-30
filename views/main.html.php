<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<style type="text/css">
div.kotae-main {
  -webkit-columns: <?php echo KOTAE_NUM_COLUMNS?> 150px;
     -moz-columns: <?php echo KOTAE_NUM_COLUMNS?> 150px;
          columns: <?php echo KOTAE_NUM_COLUMNS?> 150px;
  -webkit-column-gap: 4em;
     -moz-column-gap: 4em;
          column-gap: 4em;
  -webkit-column-rule: 1px dotted #ddd;
     -moz-column-rule: 1px dotted #ddd;
          column-rule: 1px dotted #ddd;
}

div.kotae-cat {	
	overflow: hidden;	
} 
</style>

<div class="kotae-main">
	<?php foreach($cats as $cat):
		if(empty($cat->posts)) continue;
		$params = array('kotae_topic' => $cat->id);
		$target_url = add_query_arg( $params, $permalink );?>
		<div class="kotae-cat">
			<h3><?php echo stripslashes($cat->name);?></h3>
			<?php if(count($cat->subs)):?>
				<ul>
					<?php foreach($cat->subs as $sub):
						$params = array('kotae_topic' => $sub->id);
						$sub_url = add_query_arg( $params, $permalink );?>
						<li><a href="<?php echo $sub_url?>"><?php echo stripslashes($sub->name);?></a></li>
					<?php endforeach;?>
				</ul>
			<?php else:?>
				<ul>
					<?php foreach($cat->posts as $article):?>
						<li><a href="<?php echo get_permalink($article->ID);?>"><?php echo stripslashes($article->post_title);?></a></li>
					<?php endforeach;?>
				</ul>
			<?php endif;?>
			<p><a href="<?php echo $target_url?>"><?php _e('View all articles', 'koate');?></a></p>
		</div>
	<?php endforeach;?>
</div>