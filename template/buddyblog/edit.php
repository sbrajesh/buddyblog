<?php
if( function_exists( 'bp_get_simple_blog_post_form' ) ):
    $form = bp_get_simple_blog_post_form( 'buddyblog-user-posts' );
                            
    $form->show();
else :?>
<?php _e( 'Please Install <a href="http://buddydev.com/plugins/bp-simple-front-end-post/"> BP Simple Front End Post Plugin to make the editing functionality work.', 'buddyblog' );?>

<?php endif; ?>
