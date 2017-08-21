jQuery(document).ready(function(){
	
	set_post_active();
	
	play_with_post_toggle();
	
});

function set_post_active() {
	
	var trigger 		= jQuery("#set-acf-wpml-post-active, .switch .rc-enable-disable.disabled"),
		container		= jQuery("body"),
		blockoptions 	= {message: '<font color="white" size="24pt"><strong>Please wait</strong></font>', theme: false, css: {border: 0, backgroundColor: 'none'}, overlayCSS: {backgroundColor:"#2e88c5"}};
	
	trigger.on('click', function(e){
		e.preventDefault();
		var ask = confirm('By settings this post as active, the current active post will become inactive. Are you sure you wish to proceed?');
		if(ask == true) {
			container.block(blockoptions);
			jQuery(".blockUI.blockOverlay").css('z-index', '10000');
			jQuery(".blockUI.blockMsg").css('z-index', '10001');
			
			var postid = jQuery(this).attr('data-postid');
			
			jQuery.ajax({
				type: "POST",
				url: postactive.ajaxurl,
				dataType: 'html',
				data: ({action: 'set_acf_wpml_post_active', postid: postid}),
				success: function(data) {
					location.reload(true); 
				}
			});
		}
	});
	
}

function play_with_post_toggle() {
	
	var trigger = jQuery(".switch .rc-enable-disable.enabled");
	
	trigger.on('click', function(e){
		e.preventDefault;
		alert('The post is already set as active.');
		return false;
	});
	
}
