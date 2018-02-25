<?php
/**
 * Template functions.
 *
 * @package buddyblog
 */

// Exit if file access directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * General functions for templating purpose.
 */

/**
 * Loads a template from theme or the plugin directory
 * It checks theme directory first. looks inside the buddyblog dir of the theme first.
 *
 * @param string $template template name.
 */
function buddyblog_load_template( $template ) {

	$template_dir = apply_filters( 'buddyblog_template_dir', 'buddyblog' );

	// check for buddyblog/template-file.php in the child theme's dir and then in parent's.
	$located = locate_template( array( $template_dir . '/' . $template ), false );

	if ( ! $located ) {
		$located = buddyblog()->get_path() . 'template/buddyblog/' . $template;
	}

	if ( is_readable( $located ) ) {
		require $located;
	}
}

/**
 * Get single post permalink
 *
 * @param int $post_id post id.
 *
 * @return string
 */
function buddyblog_get_post_url( $post_id ) {

	$bp = buddypress();

	$id_or_slug = '';

	$post = get_post( $post_id );

	if ( get_post_type( $post ) != buddyblog_get_posttype() ) {
		return get_permalink( $post_id );
	}

	if ( buddyblog_use_slug_in_permalink() ) {

		$id_or_slug = $post->post_name;

	} else {

		$id_or_slug = $post->ID;
	}

	return bp_core_get_user_domain( $post->post_author ) . $bp->buddyblog->slug . '/' . BUDDYBLOG_ARCHIVE_SLUG . '/' . $id_or_slug . '/';
}

/**
 * Get the url of the Post for editing
 *
 * @param int $post_id post id.
 *
 * @return string
 */
function buddyblog_get_edit_url( $post_id = 0 ) {

	$bp = buddypress();

	$user_id = get_current_user_id();

	if ( ! $user_id ) {
		return '';
	}

	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}
	// check if current user can edit the post.
	$post = get_post( $post_id );

	// if the author of the post is same as the loggedin user or the logged in user is admin.
	if ( $post->post_type != buddyblog_get_posttype() ) {
		return '';
	}

	if ( $post->post_author != $user_id && ! is_super_admin() ) {
		return '';
	}

	$action_name = 'edit';

	if ( current_user_can( buddyblog_get_option( 'dashboard_edit_cap' ) ) ) {
		return get_edit_post_link( $post );
	}

	// if we are here, we can allow user to edit the post.
	return bp_core_get_user_domain( $post->post_author ) . $bp->buddyblog->slug . "/{$action_name}/" . $post->ID . '/';
}

/**
 * Get the link for editing this Post
 *
 * @param int    $id post id.
 * @param string $label label.
 *
 * @return string
 */
function buddyblog_get_edit_link( $id = 0, $label = '' ) {

	if ( ! buddyblog_get_option( 'allow_edit' ) ) {
		return '';
	}

	if ( empty( $label ) ) {
		$label = __( 'Edit', 'buddyblog' );
	}

	$url = buddyblog_get_edit_url( $id );

	if ( ! $url ) {
		return '';
	}

	return "<a href='{$url}'>{$label}</a>";
}

/**
 * Get delete link for post
 *
 * @param int    $id post id.
 * @param string $label label.
 *
 * @return string
 */
function buddyblog_get_delete_link( $id = 0, $label = '' ) {

	if ( ! buddyblog_user_can_delete( $id, get_current_user_id() ) ) {
		return '';
	}

	if ( empty( $label ) ) {
		$label = __( 'Delete', 'buddyblog' );
	}

	$bp = buddypress();

	$post = get_post( $id );

	$action_name = 'delete';

	$url = bp_core_get_user_domain( $post->post_author ) . $bp->buddyblog->slug . "/{$action_name}/" . $post->ID . '/';

	return "<a href='{$url}' class='confirm' >{$label}</a>";

}

/**
 * Link to create new post
 *
 * @return string
 */
function buddyblog_get_new_url() {

	$bp = buddypress();

	$user_id = get_current_user_id();

	if ( ! $user_id ) {
		return '';
	}

	// if we are here, we can allow user to edit the post.
	return bp_core_get_user_domain( $user_id ) . $bp->buddyblog->slug . '/edit/';
}

/**
 * Just a wrapper , you may use get_permalink instead if you have the post id
 *
 * @param int $post_id post id.
 *
 * @return string Link to the single post
 */
function buddyblog_get_single_url( $post_id = 0 ) {

	if ( ! buddyblog_is_single_post() ) {
		return false;
	}

	if ( ! $post_id ) {
		$post_id = (int) bp_action_variable( 0 );
	}

	if ( ! $post_id ) {
		return '';
	}

	return get_permalink( $post_id );
}

/**
 * Generate pagination links
 *
 * @global WP_Query $wp_query
 */
function buddyblog_paginate() {

	// get total number of pages.
	global $wp_query;
	$total = $wp_query->max_num_pages;

	// only bother with the rest if we have more than 1 page!
	if ( $total > 1 ) {
		// get the current page.
		if ( ! $current_page = get_query_var( 'paged' ) ) {
			$current_page = 1;
		}

		// structure of “format” depends on whether we’re using pretty permalinks.
		$perma_struct = get_option( 'permalink_structure' );

		$format = empty( $perma_struct ) ? '&page=%#%' : 'page/%#%/';
		$base   = trailingslashit( buddyblog_get_home_url() . BUDDYBLOG_ARCHIVE_SLUG );

		if ( bp_is_buddyblog_component() ) {
			//$base = $base.'/';
		}

		echo paginate_links( array(
			'base'     => $base . '%_%',
			'format'   => $format,
			'current'  => $current_page,
			'total'    => $total,
			'mid_size' => 4,
			'type'     => 'list',
		) );
	}
}
