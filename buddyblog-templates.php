<?php

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
function buddyblog_load_template($template){
    if(file_exists(STYLESHEETPATH.'/buddyblog/'.$template))
            include_once(STYLESHEETPATH.'/buddyblog/'.$template);
    else if(file_exists(TEMPLATEPATH.'/buddyblog/'.$template))
        include_once (TEMPLATEPATH.'/buddyblog/'.$template);
    else 
        include_once BP_BUDDYBLOG_PLUGIN_DIR.'template/buddyblog/'.$template;
}


function buddyblog_get_post_url( $post_id ){
    global $bp;
    
    $id_or_slug = '';
    $post = get_post( $post_id );
    
    if( get_post_type( $post ) != buddyblog_get_posttype() )
        return get_permalink ( $post_id );
    
    if( buddyblog_use_slug_in_permalink() )
        $id_or_slug = $post->post_name;
    else 
        $id_or_slug = $post->ID;
    
    return bp_core_get_user_domain($post->post_author).$bp->buddyblog->slug. '/' . BUDDYBLOG_ARCHIVE_SLUG. '/' . $id_or_slug.'/';
    
    
}
/**
 * Get the url of the Post for editing
 * @param type $post_id
 * @return type 
 */
function buddyblog_get_edit_url($post_id=false){
    global $bp;
    $user_id=get_current_user_id();
    if(!$user_id)
        return;
    
    if(empty($post_id))
        $post_id=get_the_ID ();
     //cheeck if current user can edit the post
    $post=get_post($post_id);
    //if the author of the post is same as the loggedin user or the logged in user is admin
   
    if($post->post_type!=  buddyblog_get_posttype())
        return false;
   
   
    
    if(!($post->post_author==$user_id || is_super_admin()))
            return ;
   
       $action_name='edit';
    //if we are here, we can allow user to edit the post
    return bp_core_get_user_domain($post->post_author).$bp->buddyblog->slug."/{$action_name}/".$post->ID."/";
}

/**
 * Get the link for editing this Post
 * @param type $id
 * @param type $label
 * @return type 
 */
function buddyblog_get_edit_link($id=0,$label='Edit'){
    
    $url=buddyblog_get_edit_url($id);
    
    if(!$url)
        return;
    return "<a href='{$url}'>{$label}</a>";
}

/**
 * get delete link for Post
 * @param type $id
 * @param type $label
 * @return type 
 */
function buddyblog_get_delete_link($id=0,$label='Delete'){
    if(!buddyblog_user_can_delete($id,  get_current_user_id()))
            return;
    global $bp;
    $post = get_post( $id );
    
    $action_name='delete';
    
    $url= bp_core_get_user_domain($post->post_author).$bp->buddyblog->slug."/{$action_name}/".$post->ID."/";
    
    
   
     return "<a href='{$url}' class='confirm' >{$label}</a>";
    
}
/**
 * Link to post new Post
 * 
 * 
 * @return type 
 */

function buddyblog_get_new_url(){
    global $bp;
    $user_id=get_current_user_id();
    if(!$user_id)
        return;
    
    //if we are here, we can allow user to edit the post
    return bp_core_get_user_domain($user_id).$bp->buddyblog->slug.'/edit/';
}


/**
 * Just a wrapper , you may use get_permalink instead if you have the post id
 * 
 * @param int $post_id
 * @return string Link to the single post 
 */
function buddyblog_get_single_url($post_id=false){
    if(!buddyblog_is_single_post())
        return false;
    if(!$post_id)
        $post_id=(int)bp_action_variable (0);
    
    if(!$post_id)
        return;
    
    return get_permalink($post_id);
}





/*we are not using it in v 1.0*/

function buddyblog_comment_entry( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type )
		return false;

	if ( 1 == $depth )
		$avatar_size = 50;
	else
		$avatar_size = 25;
	?>

	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_comment_author_url(); ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => $avatar_size, 'height' => $avatar_size, 'email' => $comment->comment_author_email ) ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, $avatar_size ); ?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		<div class="comment-content">
			<div class="comment-meta">
				<p>
					<?php
						/* translators: 1: comment author url, 2: comment author name, 3: comment permalink, 4: comment date/timestamp*/
						printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said on <a href="%3$s"><span class="time-since">%4$s</span></a>', 'buddyblog' ), get_comment_author_url(), get_comment_author(), get_comment_link(), get_comment_date() );
					?>
				</p>
			</div>

			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
				 	<em class="moderate"><?php _e( 'Your comment is awaiting moderation.', 'buddyblog' ); ?></em>
				<?php endif; ?>

				<?php comment_text(); ?>
			</div>

			<div class="comment-options">
					<?php if ( comments_open() ) : ?>
						<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
					<?php endif; ?>

					<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) : ?>
						<?php printf( '<a class="button comment-edit-link bp-secondary-action" href="%1$s" title="%2$s">%3$s</a> ', get_edit_comment_link( $comment->comment_ID ), esc_attr__( 'Edit comment', 'buddyblog' ), __( 'Edit', 'buddyblog' ) ); ?>
					<?php endif; ?>

			</div>

		</div>

<?php
}
//pagination
function buddyblog_paginate(){
    /// get total number of pages
    global $wp_query;
    $total = $wp_query->max_num_pages;
    // only bother with the rest if we have more than 1 page!
    if ( $total > 1 )  {
         // get the current page
         if ( !$current_page = get_query_var('paged') )
              $current_page = 1;
         // structure of “format” depends on whether we’re using pretty permalinks
         $perma_struct=get_option('permalink_structure');
         $format = empty( $perma_struct ) ? '&page=%#%' : 'page/%#%/';
         $base = trailingslashit( buddyblog_get_home_url() . BUDDYBLOG_ARCHIVE_SLUG );
        // echo $base;
         if(bp_is_buddyblog_component()){
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
?>