<?php
	require('social.class.php');
	$ID = get_the_ID();
	
	$page_data = $wpdb->get_row('SELECT * FROM `'.$wpdb->base_prefix.'pdf_page` WHERE `page_id` = '.$ID);
	
	$share = new SocialShare($ID, json_decode($page_data->social, true));
	$barClass = "pdf_page_bar ";
	$barClass .= ($page_data->bar_position == 'Bottom') ? 'bot_bar' : ' top_bar';
	$document_class = ($page_data->bar_position == 'Bottom') ? 'd_bot_bar' : ' d_top_bar';
	
	//include HTML template
	require(plugin_dir_path(__FILE__).'/../templates/pdf_page_front_template.php');
?>