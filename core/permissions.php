<?php
/**
 * All the permissions related functions reside here
 * 
 */

/**
 * Can user publish the post
 * @param int $user_id
 * @param int $post_id
 * @return bool 
 */
function buddyblog_user_can_publish($user_id,$post_id=false){
   //super admins can always post
    if(is_super_admin())
        return true;
     if(!$user_id)
            $user_id=get_current_user_id ();
     
    $can_publish=false;
    //by default, everyone can publish, we assume
    if(is_user_logged_in())
        $can_publish=true;
    
    //has the admin set a limit on no. of posts?
    if(is_user_logged_in()&&buddyblog_limit_no_of_posts()){
     //let us find the user id
       
        //find remaining posts count
        $remaining_posts=buddyblog_get_remaining_posts($user_id);
        if($remaining_posts>0)
            $can_publish=1;

        }
        
    
      return apply_filters('buddyblog_user_can_publish',$can_publish,$user_id);
}
/**
 * Can the user unpublish the post
 * Yes if he is super admin or the author of the post
 * @param int $user_id
 * @param int $post_id
 * @return bool 
 */
function buddyblog_user_can_unpublish($user_id,$post_id){
    $can_do=false;
    //non logged in users can not post 
    if(!is_user_logged_in())
        return $can_do;
    //super admins can always post
    if(is_super_admin())
        $can_do= true;
    else{//if the use is not suepr admin but is logged in
        $post=get_post($post_id);
        if($post->post_author==$user_id)
            $can_do= true;
    }
    
    return apply_filters('buddyblog_user_can_unpublish',$can_do,$user_id,$post_id);
}
/**
 * Can the user post a new post ?
 * 
 * It will always return true, as we don't have restriction for this,
 * we do have restriction on how many post can be active though
 * 
 * @return bool
 *
 */
function buddyblog_user_can_post($user_id=false){
    //non logged in users can not post 
   $can_post=true;
    
   if(!is_user_logged_in())
        $can_post=false;
   
    return apply_filters('buddyblog_user_can_post',$can_post,$user_id);
        
  
}
/**
 * Can user edit the post
 * 
 * @return bool 
 */
function buddyblog_user_can_edit($post_id,$user_id=false){
   //if user is logged in and the post id is given only then we should proceed
    if(!$post_id||!is_user_logged_in())
        return false;
    
    if(is_super_admin())
        return true;
    
    if(!$user_id)
        $user_id=get_current_user_id ();
    
    $post=get_post($post_id);
    
    if($post->post_author==  $user_id)
        return true;
    
    return false;
}
/**
 * Can the user delete this post
 * 
 * @param type $post_id
 * @param type $user_id
 * @return bool
 */
function buddyblog_user_can_delete($post_id,$user_id=false){
     if(!$post_id&&  in_the_loop())
         $post_id=get_the_ID ();
     
    if(!$post_id||!is_user_logged_in())
        return false;
     
    if(is_super_admin())
        return true;
    
    if(!$user_id)
        $user_id=get_current_user_id ();
    
    $post=get_post($post_id);
    if($post->post_author==$user_id)
        return true;
    
    return false;
}

?>