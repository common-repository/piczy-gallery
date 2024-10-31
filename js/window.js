function openWindow(open_url,postdata){
    if (jQuery("#pz-pop-form").length == 0){
        jQuery('body').prepend('<div id="pz-pop-window"></div>');
	    jQuery('#pz-pop-window')
		    .append('<div id="pz-pop-form"></div>')
		    .css({minHeight:jQuery('body').height()});

    }

	jQuery('#pz-pop-window').animate({
		opacity: 1
	},250);

	jQuery.ajax({
		url: open_url,
		type: 'post',
		data: postdata,
		success: function(data){

			jQuery('#pz-pop-form').html(data);

			jQuery(".popform_height")
				.css({height:jQuery('#pz-pop-form').height()});

			jQuery('#pz-pop-form').show();

		}
	});

	jQuery("#pz-pop-form").on('click',function(event){
		event.stopPropagation();
	});

	jQuery("#pz-pop-window").on('click',function(){
		closeWindow();
	});

}

function closeWindow(){
	jQuery('#pz-pop-window').fadeOut(100,function(){
		jQuery('#pz-pop-window').remove();
	});
}