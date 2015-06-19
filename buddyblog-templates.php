<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * General functions for templating purpose
 *
 */
/**
 * Loads a template from theme or the plugin directory
 * It checks theme directory first
 * @param string $template 
 * looks inside the buddyblog dir of the theme first
 */
function buddyblog_load_template( $template ) {
	
    if ( file_exists( STYLESHEETPATH . '/buddyblog/' . $template ) ) {
     
		include_once STYLESHEETPATH . '/buddyblog/' . $template ;
			
	} elseif ( file_exists( TEMPLATEPATH . '/buddyblog/' . $template ) ) {
     
		include_once TEMPLATEPATH . '/buddyblog/' . $template ;
		
	} else {
		
        include_once BP_BUDDYBLOG_PLUGIN_DIR . 'template/buddyblog/' . $template;
	}	
}

/**
 * Get single post permalink
 * 
 * @param type $post_id
 * @return type
 */
function buddyblog_get_post_url( $post_id ) {
   
	$bp = buddypress();
    
    $id_or_slug = '';
	
    $post = get_post( $post_id );
    
    if( get_post_type( $post ) != buddyblog_get_posttype() ) {
     
		return get_permalink ( $post_id );
		
	}	
    
    if( buddyblog_use_slug_in_permalink() ) {
        
		$id_or_slug = $post->post_name;
		
	} else { 
     
		$id_or_slug = $post->ID;
	}
	
    return bp_core_get_user_domain( $post->post_author ) . $bp->buddyblog->slug .  '/' . BUDDYBLOG_ARCHIVE_SLUG . '/' . $id_or_slug . '/';
    
}
/**
 * Get the url of the Post for editing
 * @param type $post_id
 * @return type 
 */
function buddyblog_get_edit_url( $post_id = false ) {
	
    $bp = buddypress();
	
    $user_id = get_current_user_id();
	
    if ( ! $user_id ) {
        return;
	}
	
    if( empty( $post_id ) ) {
     
		$post_id = get_the_ID();
		
	}	
     //cheeck if current user can edit the post
    $post = get_post( $post_id );
    //if the author of the post is same as the loggedin user or the logged in user is admin
   
    if( $post->post_type != buddyblog_get_posttype() ) {
     
		return false;
	}	
   
    
    if( $post->post_author != $user_id && ! is_super_admin() ) {
		return ;
	}
	
    $action_name = 'edit';
	if( current_user_can( buddyblog_get_option( 'dashboard_edit_cap' ) ) )
		return get_edit_post_link ( $post );
	
    //if we are here, we can allow user to edit the post
    return bp_core_get_user_domain( $post->post_author ) . $bp->buddyblog->slug . "/{$action_name}/" . $post->ID . '/';
}

/**
 * Get the link for editing this Post
 * @param type $id
 * @param type $label
 * @return type 
 */
function buddyblog_get_edit_link( $id = 0, $label = 'Edit' ) {
    
	
	if( ! buddyblog_get_option( 'allow_edit' ) )
		return '';
	
    $url = buddyblog_get_edit_url( $id );
    
    if( ! $url ) {
        return '';
	}
	
    return "<a href='{$url}'>{$label}</a>";
}

/**
 * get delete link for Post
 * @param type $id
 * @param type $label
 * @return type 
 */
function buddyblog_get_delete_link( $id = 0, $label = 'Delete' ) {
	
    if( ! buddyblog_user_can_delete( $id,  get_current_user_id() ) ) {
        return;
	}
	
    $bp = buddypress();
	
    $post = get_post( $id );
    
    $action_name = 'delete';
    
    $url= bp_core_get_user_domain( $post->post_author ) . $bp->buddyblog->slug . "/{$action_name}/" . $post->ID . '/';
    
    return "<a href='{$url}' class='confirm' >{$label}</a>";
    
}
/**
 * Link to create new Post
 * 
 * 
 * @return type 
 */

function buddyblog_get_new_url() {
	
    $bp = buddypress();
	
    $user_id = get_current_user_id();
	
    if( ! $user_id ) {
        return '';
	}
    //if we are here, we can allow user to edit the post
    return bp_core_get_user_domain( $user_id ) . $bp->buddyblog->slug . '/edit/';
}


/**
 * Just a wrapper , you may use get_permalink instead if you have the post id
 * 
 * @param int $post_id
 * @return string Link to the single post 
 */
function buddyblog_get_single_url( $post_id = false ) {
	
    if( ! buddyblog_is_single_post() ) {
     
		return false;
	}	
	
    if( ! $post_id ) {
		
		$post_id = (int) bp_action_variable (0);
	}
	
    if( ! $post_id )
        return '';
    
    return get_permalink( $post_id );
}

/**
 * Generate pagination links
 * 
 * @global type $wp_query
 */
function buddyblog_paginate() {
	
    /// get total number of pages
    global $wp_query;
    $total = $wp_query->max_num_pages;
	
    // only bother with the rest if we have more than 1 page!
    if ( $total > 1 )  {
         // get the current page
		if ( !$current_page = get_query_var( 'paged' ) )
			 $current_page = 1;
		
         // structure of “format” depends on whether we’re using pretty permalinks
        $perma_struct = get_option( 'permalink_structure' );
		
        $format = empty( $perma_struct ) ? '&page=%#%' : 'page/%#%/';
        $base = trailingslashit( buddyblog_get_home_url() . BUDDYBLOG_ARCHIVE_SLUG );
        // echo $base;
        if( bp_is_buddyblog_component() ) {
             //$base = $base.'/';
    
        }
          
		echo paginate_links(array(
			'base' => $base . '%_%',
			'format' => $format,
			'current' => $current_page,
			'total' => $total,
			'mid_size' => 4,
			'type' => 'list'
         ));
    }
}
