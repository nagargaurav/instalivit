<?php
/*
Plugin Name: instalivit
Plugin URI: https://github.com/grvngr/instalivit
Description: Simple Plugin to display instagram feed
Author: Gaurav Nagar
Version: 1.0
Author URI: 
*/

//Registers activation and deactivation hooks for plugin.
register_activation_hook(__FILE__, 'instalivit_activate'); 
register_deactivation_hook( __FILE__, 'instalivit_delete');

// Adds action for ajax requests from the detail view
add_action('init', 'ajax_config');

// Adds configuration panel in admin section for the plugin settings
add_action('admin_menu', 'instalivit_admin_panel');

// Adds third party scripts
add_action( 'wp_enqueue_scripts', 'ext_script' );

// Declare global arrays for having access to arguments in the feed shortcode
$agrs_users = array();
$agrs_tags = array();

// Add shortcodes for plugin feed page and details page.
add_shortcode('instalivit', 'insta_feed');
add_shortcode('instadetail', 'insta_image_detail');


// Done for star rating jQuery SVG support
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );



function ajax_config() {
	if ( !empty($_REQUEST['ajax']) ) {
		$shortcode = sprintf('[%s]', include('includes/insta_image_detail_view.php'));
		echo do_shortcode( $shortcode );
		exit;
	}	
}

function ext_script() {
	wp_enqueue_style('style_plugin', plugins_url('css/style.css', __FILE__));
	wp_enqueue_style('style_rateyo', plugins_url('css/jquery.rateyo.min.css', __FILE__));
	wp_enqueue_script('jquery');
    wp_enqueue_script('popup_script', plugins_url('js/jquery.bpopup.min.js', __FILE__));
    wp_enqueue_script('rateyo_script', plugins_url('js/jquery.rateyo.min.js', __FILE__));
    wp_enqueue_script('popup_script', plugins_url('js/imagesloaded.pkgd.min.js', __FILE__));
    wp_enqueue_script( 'jquery-masonry' );
    wp_enqueue_script('plugin_script', plugins_url('js/script.js', __FILE__));
}

function instalivit_activate() {

	global $wpdb;
	$table_name = $wpdb->prefix . "instalivit";
	$charset = $wpdb->get_charset_collate();
	
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		image_id text NOT NULL,
		name varchar(255) NOT NULL,
		comment text NOT NULL,
		rating varchar(55) DEFAULT '' NOT NULL,
		time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
		UNIQUE KEY id (id)
	) $charset;";
	
	$wpdb->query($sql);
	
	
    $page_title = 'Image Details';
    //$page_name = 'instadetail';

    $page = array();
    $page['post_title'] = $page_title;
    $page['post_content'] = "[instadetail]";
    $page['post_status'] = 'publish';
    $page['post_type'] = 'page';
    $page['comment_status'] = 'closed';
    $page['ping_status'] = 'closed';
    $page['post_category'] = array(1);

    $page_id = wp_insert_post( $page );
	
	add_option( 'instadetail_page_id', $page_id );
    
}

function instalivit_delete() {

	global $wpdb;

	$table_name = $wpdb->prefix . "instalivit";
	$sql = "DROP TABLE $table_name;";
	
	$wpdb->query($sql);
    
    $page_id = get_option('instadetail_page_id');
    delete_option('instadetail_page_id');
    
    if( $page_id ) {
        wp_delete_post( $page_id );
    }

}

function instalivit_admin_panel() {
	add_menu_page('Instalivit Settings', 'Instalivit Settings', 'manage_options', __FILE__, 'admin_page');
}

function admin_page(){
	include_once('includes/admin_view.php');	
}

function insta_feed($atts) {

	$atts = shortcode_atts(
		array(
			'user' => '',
			'hashtag' => '',
		), $atts, 
	'instalivit' );

	$agrs_users = array();
	$agrs_tags = array();
	
	if ($atts['user']){
		$agrs_users = explode(",", $atts['user']);
	}
	
	if ($atts['hashtag']){
		$agrs_tags = explode(",", $atts['hashtag']);
	}
	
	include_once('includes/insta_feed_view.php');

}

function insta_image_detail() {
	include_once('includes/insta_image_detail_view.php');
}
