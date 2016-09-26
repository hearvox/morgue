<?php

/*
 * Plugin Name: Morgue
 * Plugin URI: http://transom.org/
 * Description: Adds new custom post status ('morgue') to save old posts, but keep them from public view.
 * Author:  Barrett Golding
 * Version: 0.1
 * Author URI: http://transom.org/
 * License: GPL2+
 * Text Domain: morgue
 * Prefix: morgue_
 */

/*******************************
 =STATUS
********************************/
/* Add custom post status ('morgue': keep post from public view). */
// http://jamescollings.co.uk/blog/wordpress-create-custom-post-status/
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

/* Adding custom post status to Publish box: Status dropdown */
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

/* Adding custom post status to Bulk and Quick Edit boxes: Status dropdown */
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

/* Add custom post status to All {Post Type} index */
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
