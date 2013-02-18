<?php

/**
 * BuddyBlog Posts Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
class BuddyBlogScreens{
    
    private static $instance;
    
    private function __construct() {
      

    }

    public static function get_instance(){
        
        if(!isset (self::$instance))
                self::$instance=new self();
        return self::$instance;
    }
    
    /**
     * Handles My Posts screen with the single post/edit post view
     */
    function my_posts(){
        
        //it may be posts archive or single post
        //if it is single post view
        if(buddyblog_is_single_post()&&bp_action_variable(1)=='delete'){
           $post_id=(int)bp_action_variable(0);
            if(buddyblog_user_can_delete($post_id,  get_current_user_id())){
               
                    wp_delete_post($post_id, true);
                    bp_core_add_message (__('Post deleted successfully'),'buddyblog');
                    //redirect
                    wp_redirect(buddyblog_get_home_url());//hardcoding bad
                    exit(0);  
            }
           else
               bp_core_add_message ('You should not perform unauthorized actions','error');
        }
            
        if(buddyblog_is_single_post())
          add_action('bp_template_content',array($this,'get_single_post_data'));
        else //list all posts by user
            add_action('bp_template_content',array($this,'get_posts_list_data'));
        
        bp_core_load_template(array('members/single/plugins'));
    }
   /**
    * New  post form
    */ 
    
    function new_post(){
        //the new post form
        add_action('bp_template_content',array($this,'get_edit_post_data'));
        bp_core_load_template(array('members/single/plugins'));
    }
   
    /**
     * Handle the edit view
     */
     function edit_post(){
        add_action('bp_template_content',array($this,'get_edit_post_data'));
         
        bp_core_load_template(array('members/single/plugins'));
    }
    
   
   
  /*
   * The single Post screen data
   */
    function get_single_post_data(){
        ob_start();
        buddyblog_load_template('single.php');
        $content=ob_get_clean();
        echo $content;
    }
    
    /**
     * List of Posts data
     */
    function get_posts_list_data(){
        ob_start();
        buddyblog_load_template('posts.php');
        $content=ob_get_clean();
        echo $content;
    }
    /**
     * Edit Post data 
     */
    function get_edit_post_data(){
        ob_start();
        buddyblog_load_template('edit.php');
        $content=ob_get_clean();
        echo $content;
    }
    
  
 
}   


BuddyBlogScreens::get_instance();
?>