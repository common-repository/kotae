// called in admin to add new category by ajax
function kotaeAddCategory(val) {
	if(val == '') {
		alert(kotae_i18n.enter_category_name);
		jQuery('#newKotaeCatName').focus();
		return false;
	}
	jQuery.post(ajaxurl, {"name" : val, 'action' : 'kotae_ajax', 'do' : 'add_category'}, function(msg){
		parts = msg.split("|");
		if(parts[0] == 'OK') {
			// add to the div with cats
			input = '<input type="checkbox" name="kotae_cats[]" value="'+parts[1]+'" checked="true"> ' + val + ' <br />';
			jQuery('#kotaeCats').append(input);
			jQuery('#newKotaeCatName').val('');
		}
		else alert(msg);
	});
}

Kotae = {};
Kotae.ajax_url = '';
Kotae.plugin_url = '';

// save the rating on server
Kotae.saveRating = function(rating, postID) {
	var data = {"rating" : rating, "post_id" : postID, 'action': 'kotae_ajax', 'do': 'store_rating'};
	jQuery.post(Kotae.ajax_url, data);
}

Kotae.thumbs = function(dir, postID) {	
	if(dir == 5) {		
		jQuery('#thumbsUp'+postID).attr('src', Kotae.plugin_url + "/img/thumbs-up-sel.png");
		jQuery('#thumbsDown'+postID).attr('src', Kotae.plugin_url + "/img/thumbs-down.png");
		this.saveRating(5, postID);
	} else {
		this.saveRating(1, postID);
		jQuery('#thumbsUp'+postID).attr('src', Kotae.plugin_url + "/img/thumbs-up.png");
		jQuery('#thumbsDown'+postID).attr('src', Kotae.plugin_url + "/img/thumbs-down-sel.png");
	}
}