<?php
/**
 * Plugin Name: BuddyBlog
 * Version: 1.1.2
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Plugin URI: http://buddydev.com/plugins/buddyblog/
 * Description: Allow users to post/edit/manage blog posts from their BuddyPress profile 
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BP_BUDDYBLOG_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'BP_BUDDYBLOG_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

if( ! defined( 'BUDDYBLOG_ARCHIVE_SLUG' ) )
    define( 'BUDDYBLOG_ARCHIVE_SLUG', 'my-posts' );

/**
 * Include the component loader
 */
function buddyblog_load_component() {
    
	$path = plugin_dir_path( __FILE__ );
	
    include_once $path . 'buddyblog-loader.php';
	
	if( is_admin() && ! defined( 'DOING_AJAX' ) )
		require_once $path . 'admin/admin.php';
	
}
add_action( 'bp_include', 'buddyblog_load_component' );

/**
 * BuddyBlog Installation Routine
 * Does Nothing at the moment
 * @global type $wpdb
 * @global type $bp 
 */
function buddyblog_install() {
    
	$default = array(
		//'root_slug'			=> 'buddyblog',
        'post_type'				=> 'post',
		'post_status'			=> 'publish',
		'comment_status'		=> 'open',
		'show_comment_option'	=> 1,
		'custom_field_title'	=> '',
		'enable_taxonomy'		=> 1,
		'allowed_taxonomies'	=> 1,
		'enable_category'		=> 1,
		'enable_tags'			=> 1,
		'show_posts_on_profile' => false,
		'limit_no_of_posts'		=> false,
		'max_allowed_posts'		=> 20,
		'publish_cap'			=> 'read',
		'allow_unpublishing'	=> 1,//subscriber //see https://codex.wordpress.org/Roles_and_Capabilities
		'post_cap'				=> 'read',
		'allow_edit'			=> 1,
		'allow_delete'			=> 1,
		
		//'enabled_tags'			=> 1,
        //'taxonomies'		=> array( 'category' ),
        'allow_upload'		=> false,
        'max_upload_count'	=> 2
    );
	
	if( ! get_site_option( 'buddyblog-settings' ) )
		add_site_option( 'buddyblog-settings', $default );
	
}
register_activation_hook( __FILE__, 'buddyblog_install' );

add_action( 'bp_init', 'buddyblog_load_textdomain', 2 );
    //localization
function buddyblog_load_textdomain() {


	$locale = get_locale();

	// if load .mo file
	if ( ! empty( $locale ) ) {
		$mofile_default = sprintf( '%slanguages/%s.mo', plugin_dir_path( __FILE__ ), $locale );

		$mofile = apply_filters( 'buddyblog_load_mofile', $mofile_default );
		// make sure file exists, and load it
		if ( file_exists( $mofile ) ) {
			load_textdomain( 'buddyblog', $mofile );
		}
	}
}

/**
 * Load comment reply script on single post
 */
//ahh, let us not use it in the first release, just let us know what people say
//add_action('bp_enqueue_scripts','buddyblog_load_comment_js');
function buddyblog_load_comment_js() {
	
    if( bp_is_current_component( 'buddyblog' ) && bp_is_current_action( 'my-posts' ) ) {
     
		wp_enqueue_script( 'comment-reply' );
	}	
}
