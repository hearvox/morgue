<?php

/*
Plugin Name: Morgue
Plugin URI:  http://hearingvoices.com/tools/morgue
Description: For archiving posts away from public view. Adds new custom post status ('morgue') to save old posts, but prevent them from being publicly accessible.
Author:      Barrett Golding
Version:     0.1.1
Author URI:  http://hearingvoices.com/
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: morgue
Domain Path: /languages
Prefix:      morgue

Morgue is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Morgue is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Morgue. If not, see:
http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/

/* ------------------------------------------------------------------------ *
 * Custom Post Status: 'morgue'.
 * ------------------------------------------------------------------------ */

/**
 * Register new custom post status ('morgue').
 *
 * Settings save post but prevent it from public access.
 *
 * @link  https://developer.wordpress.org/reference/functions/register_post_status/
 */
function morgue_post_status(){
     register_post_status( 'morgue', array(
          'label'                     => _x( 'Morgue', 'morgue' ),
          'public'                    => false,
          'exclude_from_search'       => true,
          'show_in_admin_all_list'    => true,
          'show_in_admin_status_list' => true,
          'label_count'               => _n_noop( 'Morgue <span class="count">(%s)</span>', 'Morgue <span class="count">(%s)</span>' )
     ) );
}
add_action( 'init', 'morgue_post_status' );

/**
 * Add custom post status to edit=post Publish box in the Status dropdown.
 *
 * @link  http://jamescollings.co.uk/blog/wordpress-create-custom-post-status/
 */
function morgue_post_status_list() {
     global $post;
     $complete = '';
     $label = '';
     if ( $post->post_type == 'post' || $post->post_type == 'page' ) {
          if( $post->post_status == 'morgue' ){
               $complete = ' selected=\"selected\"';
               $label = '<span id=\"post-status-display\"> Morgue</span>';
          }
          echo '
          <script>
          jQuery(document).ready(function($){
               $("select#post_status").append("<option value=\"morgue\" ' . $complete . '>Morgue</option>");
               $(".misc-pub-section label").append("'.$label.'");
          });
          </script>
          ';
     }
}
add_action( 'admin_footer-post.php', 'morgue_post_status_list' );

/**
 * Add custom post status to Bulk and Quick Edit boxes in the Status dropdown.
 *
 * @link  https://rudrastyh.com/wordpress/custom-post-status-quick-edit.html
 */
function morgue_post_status_bulk() {
          echo '
          <script>
          jQuery(document).ready(function($){
               $(".inline-edit-status select ").append("<option value=\"morgue\">Morgue</option>");
          });
          </script>
          ';
}
add_action( 'admin_footer-edit.php', 'morgue_post_status_bulk' );

/**
 * Add custom post status to All {Post Type} index.
 *
 * @link  http://jamescollings.co.uk/blog/wordpress-create-custom-post-status/
 */
function morgue_post_status_display( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if ( $arg != 'morgue' ) {
          if ($post->post_status == 'morgue') {
               return array( 'Morgue' );
          }
     }
    return $states;
}
add_filter( 'display_post_states', 'morgue_post_status_display' );

/**
 * Close comments and pings when content gets Morgue status.
 *
 * @link https://wordpress.org/plugins/archived-post-status/developers/
 * @action save_post
 *
 * @param int     $post_id
 * @param WP_Post $post
 * @param bool    $update
 */
function morgue_save_post( $post_id, $post, $update ) {
     if ( wp_is_post_revision( $post ) ) {
          return;
     }

     if ( 'morgue' === $post->post_status ) {
          // Unhook to prevent infinite loop
          remove_action( 'save_post', __FUNCTION__ );

          $args = array(
               'ID'             => $post->ID,
               'comment_status' => 'closed',
               'ping_status'    => 'closed',
          );

          wp_update_post( $args );

          // Add hook back again
          add_action( 'save_post', __FUNCTION__, 10, 3 );
     }
}
add_action( 'save_post', 'morgue_save_post', 10, 3 );
