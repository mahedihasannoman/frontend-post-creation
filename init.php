<?php
/**
 * Plugin Name: Frontend Post Creation
 * Version: 1.0.0
 * Plugin URI: http://example.com
 * Description: Simply allow user to create posts from frontend.
 * Author: Mahedi Hasan
 * Author URI: http://example.com
 * Text Domain: fpc
 * Domain Path: /languages/
 * License: GPL v3
*/

 if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  No cheating.';
	exit;
}

//defining constants
define( 'FPC_VERSION', '1.0.0' );
define( 'FPC_PRFX', 'fpc' );
define( 'FPC__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FPC__PLUGIN_URL', plugin_dir_url(__FILE__) );

//adding required classes
require_once( FPC__PLUGIN_DIR . 'libs/class.fpc.php' );
require_once( FPC__PLUGIN_DIR . 'libs/class.helper.php' );
require_once( FPC__PLUGIN_DIR . 'libs/class.walker.php' );

/*
* @Callback: plugins_loaded
* @Description: Register plugin text domain.
* @Author - Mahedi Hasan
* @Since 1.0.0
*/
function fpc_lang_init() {
	load_plugin_textdomain( 'fpc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'fpc_lang_init' );

//pluing activation hook
register_activation_hook( __FILE__, array( 'Fpchelper', 'plugin_activation' ) );
//plugin deactivation hook
register_deactivation_hook( __FILE__, array( 'Fpchelper', 'plugin_deactivation' ) );


/*
* @Callback: bp_after_has_members_parse_args
* @Description: Exclude admin users from BuddyPress Members List.
* @Author - Mahedi Hasan
* @Since 1.0.0
*/
function fpc_remove_admin_member_from_buddy_member_page( $args ) {
	// do not exclude in admin.
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return $args;
	}
	$excluded = isset( $args['exclude'] ) ? $args['exclude'] : array();
	if ( ! is_array( $excluded ) ) {
		$excluded = explode( ',', $excluded );
	}
	$role     = 'administrator';// change to the role to be excluded.
	$user_ids = get_users( array( 'role' => $role, 'fields' => 'ID' ) );
	$excluded = array_merge( $excluded, $user_ids );
	$args['exclude'] = $excluded;
	return $args;
}


/*
* @Callback: admin_footer_text
* @Description: Changing WordPress's admin dashboard footer text
* @Author - Mahedi Hasan
* @Since 1.0.0
*/
function change_wp_dashboard_footer( $text ) {
	$my_theme = wp_get_theme();
	
    $text = '<p>'.$my_theme->get( 'Name' ).' | '.$my_theme->get( 'Version' ).'</p>' ;
     
return $text;
}

//filter for changing WordPress's admin dashboard footer text
add_filter('admin_footer_text', 'change_wp_dashboard_footer');

//Lets Go
Fpcmain::get_instance();


