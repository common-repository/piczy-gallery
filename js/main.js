jQuery(function(){

	jQuery(".pz_image").on('click',function(){
		open_image(this);
	});

});

function open_image(elm){
	var picture_id = jQuery(elm).data('picture_id');
	var picture_url = jQuery(elm).data('picture_url');
	var group_id = jQuery(elm).data('group_id');
	var return_url = jQuery(elm).data('return_url');
	if (picture_id != 0){
		var data = {
			group_id: group_id,
			picture_id: picture_id,
			return_url: return_url
		};
		openWindow(picture_url,data);
	}
}