<?php

/**
 * All the permissions related functions reside here
 * 
 */

/**
 * Check if user can publish
 *
 * @param int      $user_id id of the user.
 * @param bool|int $post_id id of the post.
 *
 * @return bool
 */
function buddyblog_user_can_publish( $user_id, $post_id = false ) {

	$can_publish = false;

	// Super admins can always post.
	if ( is_super_admin() ) {
		return true;
	}

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// By default, everyone can publish, we assume.
	if ( is_user_logged_in() ) {
		$can_publish = current_user_can( buddyblog_get_option( 'publish_cap' ) );
	}

	// Has the admin set a limit on no. of posts?.
	if ( is_user_logged_in() && buddyblog_limit_no_of_posts() ) {
		// Let us find the user id.
		// Find remaining posts count.
		$remaining_posts = buddyblog_get_remaining_posts( $user_id );

		if ( $remaining_posts > 0 ) {
			$can_publish = 1;
		}
	}

	return apply_filters( 'buddyblog_user_can_publish', $can_publish, $user_id );
}

/**
 * Check if user can unpublish the post.
 *
 * @param bool|int $user_id id of the user.
 * @param int      $post_id id of the post.
 *
 * @return bool|mixed|void
 */
function buddyblog_user_can_unpublish( $user_id = false, $post_id ) {

	$can_unpublish = false;

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	if ( ! $post_id || ! $user_id ) {
		return false;
	}

	// Super admins can always post.
	if ( is_super_admin() ) {
		return true;
	} elseif ( buddyblog_get_option( 'allow_unpublishing' ) ) {
		$post = get_post( $post_id );
		if ( $post->post_author == $user_id ) {
			$can_unpublish = true;
		}
	}

	return apply_filters( 'buddyblog_user_can_unpublish', $can_unpublish, $user_id, $post_id );
}

/**
 * Can the user post a new post ?
 *
 * It will always return true, as we don't have restriction for this,
 * we do have restriction on how many post can be active though
 *
 * @param bool|int $user_id id of user.
 *
 * @return bool
 */
function buddyblog_user_can_post( $user_id = false ) {
	// Non logged in users can not post.
	$can_post = false;

	if ( current_user_can( buddyblog_get_option( 'post_cap' ) ) ) {
		$can_post = true;
	}

	return apply_filters( 'buddyblog_user_can_post', $can_post, $user_id );
}

/**
 * Check if user can edit the post.
 *
 * @param int      $post_id id of the post.
 * @param bool|int $user_id id of the user.
 *
 * @return bool
 */
function buddyblog_user_can_edit( $post_id, $user_id = false ) {

	$can_edit = false;

	// If user is logged in and the post id is given only then we should proceed.
	if ( ! $post_id || ! is_user_logged_in() ) {
		return false;
	}

	if ( is_super_admin() ) {
		return true;
	} elseif ( ! empty( buddyblog_get_option( 'allow_edit' ) ) ) {
		// Editing not allowed.
		$can_edit = true;
	}

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$post = get_post( $post_id );

	if ( $post->post_author == $user_id ) {
		$can_edit = true;
	}

	return apply_filters( 'buddyblog_user_can_edit', $can_edit, $post_id, $post );
}

/**
 * Check if user can delete the post.
 *
 * @param int      $post_id id of the post.
 * @param bool|int $user_id id of user.
 *
 * @return bool
 */
function buddyblog_user_can_delete( $post_id, $user_id = false ) {

	$can_delete = false;

	if ( ! $post_id && in_the_loop() ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id || ! is_user_logged_in() ) {
		return false;
	}

	if ( is_super_admin() ) {
		return true;
	} elseif ( ! buddyblog_get_option( 'allow_delete' ) ) {
		// If deleting post is disabled can be override this settings.
		$can_delete = false;
	}

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$post = get_post( $post_id );

	if ( $post->post_author == $user_id ) {
		$can_delete = true;
	}
	
	return apply_filters( 'buddyblog_user_can_delete', $can_delete, $post_id, $post );
}

