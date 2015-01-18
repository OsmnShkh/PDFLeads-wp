
function trackButton(num) {
	var $button = jQuery('#pdf_button_'+num);
	var data = {
		action: 'pdf_click_button',
		page_id: page_data.page_id,
		button: num
	};
	
	$button.on('click', function(e) {
		jQuery.post(page_data.url, data);
	})
}