<?php
/**
 * This file is used for listing the posts on profile
 */
?>

<?php if( buddyblog_user_has_posted() ):?>
<?php
    //let us build the post query
    if( bp_is_my_profile() || is_super_admin())
        $status = 'any';
    else
        $status = 'publish';

    $paged = bp_action_variable(1);
    $paged = $paged?$paged:1;
    $query_args = array(
            'author'        => bp_displayed_user_id(),
            'post_type'     => buddyblog_get_posttype(),
            'post_status'   => $status,
            'paged'         => intval( $paged )
            );

        query_posts( $query_args );
?>
    <?php if( have_posts() ):?>
    <?php 
            while( have_posts() ): the_post();
            global $post;
    ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="author-box">
                    <?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
                    <p><?php printf( _x( 'by %s', 'Post written by...', 'buddyblog' ), bp_core_get_userlink( $post->post_author ) ); ?></p>

                    <?php if ( is_sticky() ) : ?>
                        <span class="activity sticky-post"><?php _ex( 'Featured', 'Sticky post', 'buddyblog' ); ?></span>
                    <?php endif; ?>
                </div>

                <div class="post-content">
                    
                    <?php if( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ):?>
                        
                        <div class="post-featured-image">
                            <?php  the_post_thumbnail();?>
                        </div>

                    <?php endif;?>

                    <h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddyblog' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

                    <p class="date"><?php printf( __( '%1$s <span>in %2$s</span>', 'buddyblog' ), get_the_date(), get_the_category_list( ', ' ) ); ?></p>

                    <div class="entry">

                        <?php the_content( __( 'Read the rest of this entry &rarr;', 'buddyblog' ) ); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddyblog' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
                    </div>

                    <p class="postmetadata"><?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddyblog' ), ', ', '</span>' ); ?> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddyblog' ), __( '1 Comment &#187;', 'buddyblog' ), __( '% Comments &#187;', 'buddyblog' ) ); ?></span></p>

                    <div class="post-actions">
                        <?php echo buddyblog_get_post_publish_unpublish_link(get_the_ID());?>
                        <?php echo buddyblog_get_edit_link();?>
                        <?php echo buddyblog_get_delete_link();?>
                    </div>     
                </div>

			</div>
                   
        <?php endwhile;?>
            <div class="pagination">
                <?php buddyblog_paginate(); ?>
            </div>
    <?php else:?>
            <p><?php _e( 'There are no posts by this user at the moment. Please check back later!', 'buddyblog' );?></p>
    <?php endif;?>

    <?php 
       wp_reset_postdata();
       wp_reset_query();
    ?>

<?php elseif( bp_is_my_profile() ):?>
    <p> <?php _e( "You haven't posted anything yet.", 'buddyblog' );?> <a href="<?php echo buddyblog_get_new_url();?>"> <?php _e( 'New Post', 'buddyblog' );?></a></p>

<?php endif; ?>
