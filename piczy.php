<?php
/*
  Plugin Name: Piczy Gallery integration
  Plugin URI: http://piczy.net/developer/wordpress/
  Description: The easyest way to show galleries on your website.
  Version: 1.2.1
  Author: Inprovo
  Author URI: http://www.inprovo.nl/
 */

require_once "piczy_api_v1.3.php";

add_action('admin_menu', 'piczy_menu');
add_action('admin_init', 'piczy_init');

add_shortcode('piczy', 'piczy_func');

wp_register_script('piczy_plugin', plugins_url('piczy-gallery/js/window.js' ) );
wp_enqueue_script('piczy_plugin');

wp_register_style('piczy_plugin', plugins_url('piczy-gallery/css/front_style.css' ) );
wp_enqueue_style('piczy_plugin');

wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );

add_action( 'wp_footer', 'piczy_ajax_javascript' );

add_action("wp_ajax_piczy_picture", "piczy_picture");
add_action("wp_ajax_nopriv_piczy_picture", "piczy_picture");

add_action("wp_ajax_piczy_action", "piczy_action");
add_action("wp_ajax_nopriv_piczy_action", "piczy_action");

function piczy_picture() {

	require_once "piczy_picture.php";
	die();

}

function piczy_action() {

	require_once "piczy_action.php";
	die();

}

function piczy_ajax_javascript(){

	?>
	<script type="text/javascript">
		jQuery(function(){

			jQuery(".pz_image").on('click',function(){
				open_image(this);
			});

		});

		function open_image(elm){
			var picture_id = jQuery(elm).data('picture_id');
			var group_id = jQuery(elm).data('group_id');
			var return_url = jQuery(elm).data('return_url');
			if (picture_id != 0){
				var data = {
					action: 'piczy_picture',
					group_id: group_id,
					picture_id: picture_id,
					return_url: return_url
				};
				openWindow('/wp-admin/admin-ajax.php',data);
			}
		}
	</script>
	<?php

}

function piczy_func( $atts ) {
	return piczy_front($atts);;
}

function piczy_menu() {
	add_menu_page("Piczy gallery", "Piczy gallery", "add_users", "piczy", "piczy_gallery", plugins_url('piczy-gallery/img/icon_small.png'), "10");
	add_submenu_page("piczy","Gallery settings", "Gallery settings", "add_users", "piczy_gallery_settings", "piczy_gallery_settings", "", "10");
	add_submenu_page("piczy","API Settings", "API Settings", "add_users", "piczy_api_settings", "piczy_settings", "", "1");
}

require_once "piczy_front.php";
require_once "piczy_settings.php";
require_once "piczy_gallery.php";
require_once "piczy_gallery_settings.php";

function piczy_field($params) {
	$params = (object)$params;

	$name = $params->name;
	$size = $params->size;

	echo '<input id="' . $name . '" name="' . $name . '" size="' . $size . '" type="text" value="' . get_option($name) . '" />';
}

function piczy_init() {
	init_settings();
	init_gallery_settings();
	init_gallery();
}

function pz_nice_date($time){
	$time = strtotime($time);
	$time = time() - $time; // to get the time since that moment

	$tokens = array(
		31536000 => 'year',
		2592000 => 'month',
		604800 => 'week',
		86400 => 'day',
		3600 => 'hour',
		60 => 'minute',
		1 => 'second'
	);

	foreach ($tokens as $unit => $text) {
		if ($time == 0){
			return 'just posted';
		}else{
			if ($time < $unit){
				continue;
			} else {
				$units = floor($time / $unit);
				if ($units == 2 && $text == 'day'){
					$text = 'yesterday';
				}
				return $units . ' ' . $text . (($units > 1) ? 's' : '') . ' ago';
			}
		}
	}

}
