jQuery(document).ready(function(){
	
	acfwpml_tabs();
	
});

function acfwpml_tabs() {
	
	var tabtrigger = jQuery('.acfwpml-tab-wrapper .nav-tab'),
		tab = jQuery('.acfwpml-main-container .acfwpml-options-block');
	
	tabtrigger.on('click', function() {
		var tabtriggerid = jQuery(this).attr('data-tabid');
		
		tabtrigger.removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
		
		tab.removeClass('acfwpml-active-block');
		tab.hide();
		jQuery('#acfwpml_tab_'+tabtriggerid).show();
		
	});
	
}
