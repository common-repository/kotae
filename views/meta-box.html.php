<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<p><?php echo $rating_text;?></p>

<p>
	<b><?php _e('Knowledge Base Categories:','kotae');?></b>
	
	<div id="kotaeCats">
	<?php foreach($cats as $cat):?>
		<input type="checkbox" name="kotae_cats[]" value="<?php echo $cat->id?>" <?php if(strstr($kotae_cats, '|'.$cat->id.'|')) echo 'checked'?>> <?php echo stripslashes($cat->name);?>
		<br />
		<?php foreach($cat->subs as $sub):?>
			<input style="margin-left:30px;" type="checkbox" name="kotae_cats[]" value="<?php echo $sub->id?>" <?php if(strstr($kotae_cats, '|'.$sub->id.'|')) echo 'checked'?>> <?php echo stripslashes($sub->name);?><br />
		<?php endforeach;
	 endforeach;?>
	</div>
	<p><?php _e('Add new category:', 'kotae');?> <input type="text" name="kotae_cat_name" id="newKotaeCatName"> <input type="button" value="<?php _e('Add category', 'kotae');?>" onclick="kotaeAddCategory(this.form.kotae_cat_name.value);"></p>	
	
	<p><?php _e('If you assign this post to some of the categories, it will be included in your knowledge base.', 'kotae');?></p>
</p>