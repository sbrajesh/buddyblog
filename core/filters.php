<?php

//we only need this if we don't want posts to be part of the main site blog and show it on user profile instead
//fix the redirect for comments posted on single post page
//comment posting a lil bit better
add_action('comment_form','buddyblog_fix_comment_form_redirect' );

function buddyblog_fix_comment_form_redirect($post_id){
    $post=get_post($post_id);
    
    if( !buddyblog_show_posts_on_profile( $post ) )
       return $post_id;
   
    if($post->post_type!=  buddyblog_get_posttype())
        return;
        $permalink= get_permalink($post_id);
    ?>
    <input type="hidden" name="redirect_to" value="<?php echo esc_url($permalink);?>" />

 <?php
}

/**
 * Get the current editable post id
 * 
 * Used with buddyPress simple front end post editing plugin
 * 
 * @return int id of the post to edit
 */
function buddyblog_get_editable_post_id($id){
    
    $action=bp_current_action();
    if(bp_is_buddyblog_component()&&($action=='edit')){
          $id=bp_action_variable (0);
    }
    return intval ($id);
    
}
add_filter('bpsp_editable_post_id','buddyblog_get_editable_post_id');


//filter success message for Front end post editor

add_filter('bsfep_post_success_message','buddyblog_filter_posting_message',10,4);
function buddyblog_filter_posting_message($message,$post_id,$post_type_obj,$form){
    if($form->post_type!=  buddyblog_get_posttype())
        return $message;
    
    if(!buddyblog_is_post_published($post_id)){
        //if the job is not active, let us know that to the user
        $message='Your Post Was saved!';
        
        
    }
    
    return $message;
}
/**
 * Update the post status based on current user caps
 * If the current user can publish, we will set the status to publish otherwise the status is set to draft by default
 */
//NOT Needed
//add_action('bsfep_post_saved','buddyblog_publish_unpublish_on_save');
function buddyblog_publish_unpublish_on_save($post_id){
    if(!empty($_POST['post_id']))
        return;
    //check if it is job post type
   $post=get_post($post_id);
   if($post->post_type!=  buddyblog_get_posttype())
       return;
   
   
   //if it is a new post
    if(buddyblog_user_can_publish(get_current_user_id())){
    //    $active_status=1;
        
    }
    
    
    return $post_id;
}

/**
 * Fix the post permalink the_permalink to point to userprofile/buddyblog/my-posts/ section for custom post type if we are using custom post type
 */
add_filter('post_type_link','buddyblog_fix_permalink',10,4);

function buddyblog_fix_permalink($permalink, $post, $leavename,$sample){
   global $bp;
    if(!buddyblog_show_posts_on_profile($post))
        return $permalink;
    
    $type=buddyblog_get_posttype();
    
    $user_link='';
    
    if( $post->post_type == $type ){
        if($post->post_status != 'publish')
            $permalink =  buddyblog_get_edit_url ($post->ID);
        else
            $permalink = bp_core_get_user_domain($post->post_author).$bp->buddyblog->slug. '/' . BUDDYBLOG_ARCHIVE_SLUG. '/' . $post->ID.'/';
        
          }
    
    

    return $permalink;
}


//filter on post_link for changing the post permalink

add_filter('post_link','buddyblog_filter_post_permalink',10,3);
function buddyblog_filter_post_permalink($permalink,$post,$leavename){
    global $bp;
     if(!buddyblog_show_posts_on_profile($post))
         return $permalink;
    if($post->post_type!=  buddyblog_get_posttype())
        return $permalink;
     if($post->post_status != 'publish')
            $permalink =  buddyblog_get_edit_url ($post->ID);
     else
       $permalink = bp_core_get_user_domain($post->post_author).$bp->buddyblog->slug. '/' . BUDDYBLOG_ARCHIVE_SLUG. '/' . $post->ID.'/';
         
    return $permalink;
    //if we are here, we need to change that permalink
    
}

//filter edit post link

add_filter( 'get_edit_post_link','buddyblog_fix_edit_post_link',10,3);

function buddyblog_fix_edit_post_link($edit_url,$post_id,$context){
     $post=get_post($post_id);
   
     if($post->post_type!=  buddyblog_get_posttype())
       return $edit_url;
   if(is_super_admin())
       return $edit_url;
   
   if($post->post_author==  get_current_user_id())
       return   buddyblog_get_edit_url($post_id);
   
   return $edit_url;
   
}
/**
 * cap filtering to show the edit link on posts
 * 
 * We filter on the edit_post(or so) cap to check if the current user can edit this post
 * Mostly used for checking existing code
 * 
 */

function buddyblog_add_user_can_edit_cap( $allcaps, $cap, $args ) {
    $post_type_obj = get_post_type_object(buddyblog_get_posttype() );
    
    if ( $post_type_obj->cap->edit_post != $args[0] )
		return $allcaps;
    
    $post_id=$args[2];
    
    if(buddyblog_user_can_edit($post_id))
        $allcaps[$cap[0]]=true;
    
    
    
    return $allcaps;
	// give author some permissions
}
add_filter( 'user_has_cap', 'buddyblog_add_user_can_edit_cap', 0, 3 );

/**
 * Should we limit user by no. of posts he has made?
 * 
 * @return type 
 */
function buddyblog_limit_no_of_posts(){
    
    return apply_filters('buddyblog_limit_no_of_posts',false);
}
/**
 * Should we show the single post on profile
 * 
 * @return type 
 */
 function buddyblog_show_posts_on_profile($post){
     return true;
     return apply_filters('buddyblog_show_posts_on_profile',false,$post);
 }
