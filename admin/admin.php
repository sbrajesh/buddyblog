<?php
//we use options buddy
require_once dirname( __FILE__ ) . '/options-buddy/ob-loader.php';


class BuddyBlog_Admin {
 
    private $page;
    
    public function __construct() {
		
        //create a options page
        //make sure to read the code below
        $this->page = new OptionsBuddy_Settings_Page( 'buddyblog-settings' );
        $this->page->set_bp_mode();//make it to use bp_get_option/bp_update_option
        
        
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu') );
        add_action( 'admin_footer', array( $this, 'admin_css' ) );
    }

    public function admin_init() {

        //set the settings
        
        $page = $this->page;
        //add_section
        //you can pass section_id, section_title, section_description, the section id must be unique for this page, section descriptiopn is optional
        $page->add_section( 'basic_section', __( 'Settings', 'buddyblog' ), __('Settings for BuddyBlog.', 'buddyblog' ) );
        
        
		$post_types = get_post_types( array( 'public'=> true ) );//public post types
		
		$post_type_options = array();
		
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$post_type_options[$post_type] = $post_type_object->labels->name;
		}
		
		$post_statuses = array(
			'publish'	=> __( 'Published', 'buddyblog' ),
			'draft'		=> __( 'Draft', 'buddyblog' )
		);
		
		$comment_statuses = array(
			'open'	=> __( 'Open', 'buddyblog' ),
			'close'	=> __( 'Closed', 'buddyblog' )
		);
		
		$default_post_type = buddyblog_get_posttype()? buddyblog_get_posttype() : 'post';

		$taxonomies = get_object_taxonomies( $default_post_type );
		
		if( isset( $taxonomies['post_format'] ) ) {
			unset( $taxonomies['post_format'] );
		}
		$tax= array();
		
		foreach( $taxonomies  as $taxonomy ) {
			$tax_object = get_taxonomy( $taxonomy );
			$tax[$taxonomy] = $tax_object->labels->name;
		}
		
        //add fields
        $page->get_section('basic_section')->add_fields(array( //remember, we registered basic section earlier
                array(
                    'name'		=> 'post_type',
                    'label'		=> __( 'Blog Post Type', 'buddyblog' ),//you already know it from previous example
                    'desc'		=> __( 'Set the post type for user blog.', 'buddyblog' ),// this is used as the description of the field
                    'type'		=> 'select',
                    'default'	=> $default_post_type,
                    'options'	=> $post_type_options
                ),
               
                array(
                    'name'		=> 'post_status',
                    'label'		=> __( 'Default post status', 'buddyblog' ),
                    'desc'		=> __( 'What should be the post status when user submits the form?', 'buddyblog' ),
                    'type'		=> 'select',
                    'default'	=> 'publish',
                    'options'	=> $post_statuses,
                    
                ),
                array(
                    'name'		=> 'comment_status',
                    'label'		=> __( 'Comment status?', 'buddyblog' ),
                    'desc'		=> __( 'Do you want to allow commenting on user posts?', 'buddyblog' ),
                    'type'		=> 'select',
                    'default'	=> 'open',
                    'options'	=> $comment_statuses,
                    
                ),
                array(
                    'name'		=> 'show_comment_option',
                    'label'		=> __( 'Allow post author to enable/disable comment?', 'buddyblog' ),
                    'desc'		=> __( 'If you enable, A user will be able to change the comment status for his/her post.', 'buddyblog' ),
                    'type'		=> 'radio',
                    'default'	=> 1,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                    
                ),
                array(
                    'name'		=> 'enable_taxonomy',
                    'label'		=> __( 'Enable Taxonomy?', 'buddyblog' ),
                    'desc'		=> __( 'If you enable, users will be able to select terms from the selected taxonomies.', 'buddyblog' ),
                    'type'		=> 'radio',
                    'default'	=> 1,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                    
                ),
                array(
                    'name'		=> 'allowed_taxonomies',
                    'label'		=> __( 'Select allowed taxonomies', 'buddyblog' ),
                    'desc'		=> __( 'Please check the taxonomies you want users to be able to attach to their post.', 'buddyblog' ),
                    'type'		=> 'multicheck',
                    'default'	=> 'category',
                    'options'	=> $tax,
                    
                ),
                array(
                    'name'		=> 'show_posts_on_profile',
                    'label'		=> __( 'Show single posts on user profile?', 'buddyblog' ),
                    'desc'		=> __( 'If you enable it, the permalink to single post will be something like http://yoursite.com/members/username/buddyblog/postname.', 'buddyblog' ),
                    'type'		=> 'radio',
                    'default'	=> 0,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                    
                ),
            
                array(
                    'name'		=> 'limit_no_of_posts',
                    'label'		=> __( 'Limit number of posts a user can create?', 'buddyblog' ),
                    'desc'		=> __( 'If you enable it, You can control the allowed number of posts from the next option.', 'buddyblog' ),
                    'type'		=> 'radio',
                    'default'	=> 0,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                    
                ),
                array(
                    'name'		=> 'max_allowed_posts',
                    'label'		=> __( 'How many posts a user can create?', 'buddyblog' ),
                    'desc'		=> __( 'Only applies if you have enabled the limit on posts from above option.', 'buddyblog' ),
                    'type'		=> 'text',
                    'default'	=> 10,
                                        
                ),
                array(
                    'name'		=> 'publish_cap',
                    'label'		=> __( 'Which capability is required for pusblishing?', 'buddyblog' ),
                    'desc'		=> __( 'Please check for https://codex.wordpress.org/Roles_and_Capabilities allowed capabilities.', 'buddyblog' ),
                    'type'		=> 'text',
                    'default'	=> 'read',//subscriber
                                        
                ),
                array(
                    'name'		=> 'allow_unpublishing',
                    'label'		=> __( 'Allow users to unpublish their own post?', 'buddyblog' ),
                    'desc'		=> '',
                    'type'		=> 'radio',
                    'default'	=> 0,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                                        
                ),
                array(
                    'name'		=> 'post_cap',
                    'label'		=> __( 'Which capability is required for creating post?', 'buddyblog' ),
                    'desc'		=> __( 'Please check for https://codex.wordpress.org/Roles_and_Capabilities allowed capabilities.', 'buddyblog' ),
                    'type'		=> 'text',
                    'default'	=> 'read',//subscriber
                                        
                ),
                array(
                    'name'		=> 'allow_edit',
                    'label'		=> __( 'Allow user to edit their post?', 'buddyblog' ),
                    'desc'		=> __( 'if you disable it, user will not be able to edit their own post.', 'buddyblog' ),
                    'type'		=> 'radio',
                    'default'	=> 1,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                ),    
            
                array(
                    'name'		=> 'dashboard_edit_cap',
                    'label'		=> __( 'Which capability can edit post in backend(WordPress Dashboard)?', 'buddyblog' ),
                    'desc'		=> __( 'User with these capabilities will nto be redirected to front end editor for editing post., user will not be able to edit their own post.', 'buddyblog' ),
                    'type'		=> 'text',
                    'default'	=> 'publish_posts',
                ),    
            
                array(
                    'name'		=> 'allow_delete',
                    'label'		=> __( 'Allow user to delete their post?', 'buddyblog' ),
                    'desc'		=> __( 'if you disable it, user will not be able to delete their own post.', 'buddyblog' ),
                    'type'		=> 'radio',
                    'default'	=> 1,
                    'options'	=> array(
							1 => __( 'Yes', 'buddyblog' ),
							0 => __( 'No', 'buddyblog' ),  
                    ),
                ),    
            
				
               
            ));
        
      
       
        $page->init();
        
    }

    public function admin_menu() {
        add_options_page( __( 'BuddyBlog Settings', 'buddyblog' ), __( 'BuddyBlog', 'buddyblog' ), 'manage_options', 'buddyblog', array( $this->page, 'render' ) );
    }

    

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    
    public function admin_css() {
        
        if( ! isset( $_GET['page'] ) || $_GET['page'] != 'buddyblog' )
            return;
        
        ?>

<style type="text/css">
    .wrap .form-table{
        margin:10px;
    }
    
</style>

   <?php     
        
    } 


}

new BuddyBlog_Admin();