<?php

add_action('buddyblog_before_blog_post', 'buddyblog_disable_bp_comment_filter' );

function buddyblog_disable_bp_comment_filter() {
    
    if( has_filter( 'comments_open', 'bp_comments_open' ) )
            remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );
}
add_action('buddyblog_after_blog_post', 'buddyblog_enable_bp_comment_filter' );

function buddyblog_enable_bp_comment_filter() {
    
    if( function_exists( 'bp_comments_open' ) )
            add_filter( 'comments_open', 'bp_comments_open', 10, 2 );
}