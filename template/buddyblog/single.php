<?php if( buddyblog_user_has_posted() ): ?>
<?php
    if( bp_is_my_profile() || is_super_admin() )
        $status = 'any';
    else
        $status = 'publish';

    $query_args = array(
            'author'        => bp_displayed_user_id(),
            'post_type'     => buddyblog_get_posttype(),
            'post_status'   => $status,
            'p'             => intval( buddyblog_get_post_id( bp_action_variable( 0 ) ) )
            );


        query_posts( $query_args );
        global $post;
       // global $withcomments;
       // $withcomments = true;
?>
<?php while( have_posts() ): the_post(); ?>
    
    <?php
        //used to unhook BuddyPress Theme compatibility comment closing function
        do_action( 'buddyblog_before_blog_post' );
    ?>
    <div class="user-post">
        <h2><?php the_title();?></h2>

        <div class="post-entry">

            <p class="alignright"><?php printf( __( 'Postend on %1$s', 'buddyblog' ), get_the_time('m/j/Y') ); ?></p>

            <div class="clear"></div>
            
           <?php if( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ): ?>
                <div class="post-featured-image">
                    <?php  the_post_thumbnail();?>

                </div>

           <?php endif;?>  
            
            <div class="entry">
                    <?php the_content(  ); ?>

            </div>

            <?php echo buddyblog_get_edit_link();?>
            <div class="clear"></div>

        </div>
    <?php comments_template('/comments.php'); ?>
        <?php
            //used to hook back BuddyPress Theme compatibility comment closing function
            do_action( 'buddyblog_after_blog_post' );
        ?>
     </div>    
    <?php endwhile;?>
    <?php 
        wp_reset_postdata();
        wp_reset_query();
    ?>
    <?php else:?>
    <p> <?php _e( 'No Posts found!', 'buddyblog' );?></p>

    <?php endif; ?>
   