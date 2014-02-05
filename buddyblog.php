<?php
/**
 * Plugin Name: BuddyBlog
 * Version: 1.0.4
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Plugin URI: http://buddydev.com/plugins/buddyblog/
 * Description: Allow users to post/edit/manage blog posts from their BuddyPress profile 
 */
define('BP_BUDDYBLOG_PLUGIN_DIR',  plugin_dir_path(__FILE__));
define('BP_BUDDYBLOG_PLUGIN_URL',  plugin_dir_url(__FILE__));

if( !defined( 'BUDDYBLOG_ARCHIVE_SLUG' ) )
    define( 'BUDDYBLOG_ARCHIVE_SLUG', 'my-posts');

/**
 * Include the component loader
 */
function buddyblog_load_component(){
    
    include_once plugin_dir_path(__FILE__).'buddyblog-loader.php';
}
add_action('bp_include','buddyblog_load_component');

/**
 * BuddyBlog Installation Routine
 * Does Nothing at the moment
 * @global type $wpdb
 * @global type $bp 
 */
function buddyblog_install(){
    
//let us dance :D   
	
}
register_activation_hook(__FILE__, 'buddyblog_install' );

/**
 * Load comment reply script on single post
 */
//ahh, let us not use it in the first release, just let us know what people say
//add_action('bp_enqueue_scripts','buddyblog_load_comment_js');
function buddyblog_load_comment_js(){
    if( bp_is_current_component( 'buddyblog' ) && bp_is_current_action( 'my-posts' ) )
        wp_enqueue_script( 'comment-reply' );
}
