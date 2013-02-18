<?php if(buddyblog_user_has_posted()):?>
<?php
if(bp_is_my_profile()||  is_super_admin())
    $status='any';
else
    $status='publish';

$query_args=array('author'=>  bp_displayed_user_id(),
                  'post_type'=> buddyblog_get_posttype(),
                  'post_status'=>$status,
                  'p'=>  intval(bp_action_variable(0))
        );


        query_posts($query_args);
?>
<?php while(have_posts()):the_post();?>
<div class="user-post">
<h2><?php the_title();?></h2>

<div class="post-entry">
    
    <p class="alignright"><?php printf( __( 'Postend on %1$s', 'buddyblog' ), get_the_time('m/j/Y') ); ?></p>

    <div class="clear"></div>
   
    <div class="entry">
            <?php the_content(  ); ?>

    </div>
    
    <?php echo buddyblog_get_edit_link();?>
    <div class="clear"></div>

</div>
<?php comments_template('/comments.php'); ?>
<?php endwhile;?>
<?php wp_reset_postdata();?>
<?php else:?>
<p>No Posts found!</p>

<?php endif; ?>
</div>