<?php

//menu items
add_action('admin_menu','cl_register_modifymenu');
function cl_register_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('CL Verification', //page title
	'CL Verification', //menu title
	'manage_woocommerce', //capabilities
	'filter_invalidate_cl_order', //menu slug
	'filter_invalidate_cl_order', //function
                '', // icon url
                40
	);
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Save Invalidate Order', //page title
	'Save Invalidate Order', //menu title
	'manage_woocommerce', //capability
	'saved_invalidate_cl_order', //menu slug
	'saved_invalidate_cl_order'); //function
}