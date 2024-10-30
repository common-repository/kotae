<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap">
	<h1><?php _e('Manage Knowledge Base Categories', 'kotae');?></h1>
	
	<?php if(!empty($parent->id)) :?>
		<h2><?php printf(__('Managing subcategories of %s', 'kotae'), stripslashes($parent->name));?></h2>
		<p><a href="admin.php?page=kotae_cats"><?php _e('Back to main categories', 'kotae');?></a></p>
	<?php endif;?>
	
	<form method="post">
		<p><?php _e('Category name:', 'kotae');?> <textarea name="name" rows="1" cols="30"></textarea>
		<input type="submit" name="add" value="<?php _e('Add Category', 'kotae');?>"></p>
		<input type="hidden" name="parent_id" value="<?php echo empty($parent->id) ? 0 : $parent->id;?>">
		<?php wp_nonce_field('kotae_cats');?>
	</form>
	
	<?php foreach($cats as $cat):?>
		<form method="post">
			<p><?php _e('Category name:', 'kotae');?> <textarea name="name" rows="1" cols="30"><?php echo stripslashes($cat->name);?></textarea>
			<?php _e('Order:', 'kotae');?> <input type="text" size="3" name="sort_order" value="<?php echo $cat->sort_order?>">
			<?php if(empty($parent->id)):?>
				<a href="admin.php?page=kotae_cats&parent_id=<?php echo $cat->id?>"><?php _e('manage subcategories', 'kotae');?></a>
			<?php endif;?>			
			
			<input type="submit" name="save" value="<?php _e('Save', 'kotae');?>">
			<input type="button" value="<?php _e('Delete', 'kotae');?>" onclick="kotaeConfirmDel(this.form);"></p>
			<input type="hidden" name="id" value="<?php echo $cat->id?>">
			<input type="hidden" name="del" value="0">
			<?php wp_nonce_field('kotae_cats');?>
		</form>
	<?php endforeach;?>
</div>

<script type="text/javascript" >
function kotaeConfirmDel(frm) {
	if(confirm("<?php _e('Are you sure?', 'kotae');?>")) {
		frm.del.value = 1;
		frm.submit();
	}
}
</script>