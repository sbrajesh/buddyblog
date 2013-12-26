<?php

/**
 * BuddyBlog Component Loader
 * 
 * should we attach it to the blog screen
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BuddyBlogCoreComponent extends BP_Component {

	/**
         * Initiate the component creation process
         */
	function __construct() {
		parent::start(
			'buddyblog',
			__( 'BuddyBlog', 'buddyblog' ),
                        untrailingslashit(plugin_dir_path(__FILE__))
		);
                
                $this->includes();//load files
                $bp->active_components[$this->id] = 1;
	}

	/**
	 * Include files
	 */
	function includes() {
		$includes = array(
                        'buddyblog-templates.php',
                       	'buddyblog-actions.php',
			'buddyblog-screens.php',
                       
			'buddyblog-functions.php',
			'buddyblog-notifications.php',
                        'buddyblog-hooks.php',
                        'core/filters.php',
                        'core/permissions.php'
                       
		);
		parent::includes( $includes );
	}

	/**
	 * Setup globals
	 */
        
	function setup_globals() {
		global $bp;

		// Define a slug, if necessary
		if ( !defined( 'BP_BUDDYBLOG_SLUG' ) )
			define( 'BP_BUDDYBLOG_SLUG', $this->id );
		
		$globals = array(
			'slug'                  => BP_BUDDYBLOG_SLUG,
			'root_slug'             => BP_BUDDYBLOG_SLUG,
			'has_directory'         => false,
			'notification_callback' => 'buddyblog_format_notifications',
			'search_string'         => __( 'Search Posts...', 'buddyblog' ),
			'global_tables'         => array()
		);

		parent::setup_globals( $globals );

		
			
		
	}

	/**
	 * Setup BuddyBar navigation
	 * Sets up user tabs
	 * 
	 */
	function setup_nav() {

		// Define local variables
		$sub_nav = array();
                $screen = BuddyBlogScreens::get_instance();//instance of the blog screen
                
                $total_posts = 0;
                if( bp_is_my_profile() ){
                    $total_posts = buddyblog_get_total_posted(bp_displayed_user_id());
                    
                    
                }else{
                    
                   $total_posts = buddyblog_get_total_published_posts(bp_displayed_user_id());
                    
                }
                
                    //
		// Add 'Blog' to the main navigation
		$main_nav = array(
			'name'                => sprintf( __( 'Blog <span>%d</span>', 'buddyblog' ), $total_posts ),
			'slug'                => $this->slug,
			'position'            => 70,
			'screen_function'     => array($screen,'my_posts'),
			'default_subnav_slug' => BUDDYBLOG_ARCHIVE_SLUG,
			'item_css_id'         => $this->id
		);
                
                //whether to link to logged in user or displayed user
                if(!bp_is_my_profile())
                    $blog_link = trailingslashit( bp_displayed_user_domain() . $this->slug );
                else
		$blog_link = trailingslashit( bp_loggedin_user_domain() . $this->slug );

		// Add the Group Invites nav item
                $sub_nav[] = array(
			'name'            => bp_is_my_profile()?__( 'My Posts', 'buddyblog' ):sprintf(__( '%s\' Posts', 'buddyblog' ),  bp_get_displayed_user_fullname()),
			'slug'            => BUDDYBLOG_ARCHIVE_SLUG,
			'parent_url'      => $blog_link,
			'parent_slug'     => $this->slug,
			'screen_function' => array($screen,'my_posts'),
			
			'position'        => 30
		);
		$sub_nav[] = array(
			'name'            => __( 'New Post', 'buddyblog' ),
			'slug'            => 'edit',
			'parent_url'      => $blog_link,
			'parent_slug'     => $this->slug,
			'screen_function' => array($screen,'new_post'),
			'user_has_access' => bp_is_my_profile(),
			'position'        => 30
		);
		
                
                
                
                
                
               
		parent::setup_nav( $main_nav, $sub_nav );
	
		
	}
	/**
	 * Set up the Toolbar
	 *
	 * 
	 */
	function setup_admin_bar() {
		global $bp;

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$user_domain = bp_loggedin_user_domain();
			$blog_link = trailingslashit( $user_domain . $this->slug );

			
			$title   = __( 'My Posts',             'buddyblog' );
			

			// My Posts
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => trailingslashit( $blog_link )
			);

			

			
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Sets up the title for pages and <title>
	 *
	 * @global BuddyPress $bp The one true BuddyPress instance
	 */
	function setup_title() {
		global $bp;

		if ( bp_is_buddyblog_component() ) {

			if ( bp_is_my_profile() && !bp_is_single_item() ) {

				$bp->bp_options_title = __( 'My Posts', 'buddyblog' );

			} else if ( !bp_is_my_profile() && !bp_is_single_item() ) {

				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => bp_displayed_user_id(),
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile picture of %s', 'buddyblog' ), bp_get_displayed_user_fullname() )
				) );
				$bp->bp_options_title = bp_get_displayed_user_fullname();

			// We are viewing a single group, so set up the
			// group navigation menu using the $this->current_group global.
			} 
		}

		parent::setup_title();
	}
        


 
}


/**
 * Setup BuddyBlog
 * @global type $bp 
 */
function bp_setup_buddyblog() {
	global $bp;

	$bp->buddyblog = new BuddyBlogCoreComponent();
}
add_action( 'bp_loaded', 'bp_setup_buddyblog');


