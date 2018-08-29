<?php
/*
Plugin Name:  Ege Cards
Plugin URI:   https://github.com/mortenege/ege-cards-plugin
Description:  Custom Created widget for SimpleFlying.com
Version:      20180829
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$ege_cards_config = [
  'version' => '20180829'
];

$ege_card_meta_names = [
  'callout' => 'Callout text',
  'issuer' => 'Bank/Issuer name',
  'deep_link' => 'Deep Link',
  'term_link' => 'Link to Terms',
  'annual_fee' => 'Annual Fee text',
  'bonus_value' => 'Bonus value text'
];

function ege_cards_create_post_type() {
  $plural = 'Travel Cards';
  $singular = 'Travel Card';
  $p_lower = strtolower($plural);
  $s_lower = strtolower($singular);

  $labels = [
    'name' => $plural,
    'singular_name' => $singular,
    'add_new_item' => "New $singular",
    'edit_item' => "Edit $singular",
    'view_item' => "View $singular",
    'view_items' => "View $plural",
    'search_items' => "Search $plural",
    'not_found' => "No $p_lower found",
    'not_found_in_trash' => "No $p_lower found in trash",
    'parent_item_colon' => "Parent $singular",
    'all_items' => "All $plural",
    'archives' => "$singular Archives",
    'attributes' => "$singular Attributes",
    'insert_into_item' => "Insert into $s_lower",
    'uploaded_to_this_item' => "Uploaded to this $s_lower",
  ];

  $supports = ['title', 'editor', 'thumbnail'];

  register_post_type( 'ege_card',
    array(
      'taxonomies' => array('category', 'post_tag'),
      'register_meta_box_cb' => 'ege_cards_meta_box',
      'labels' => $labels,
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-id',
      'supports' => $supports
    )
  );

  //TODO: register taxonomy
}
add_action( 'init', 'ege_cards_create_post_type' );

function ege_cards_post_meta_box_html ($post) {
  global $ege_card_meta_names;
  $field_names = $ege_card_meta_names;
  $meta = get_post_meta($post->ID);

  $field_values = [];
  foreach ($field_names as $name => $text) {
    if (isset($meta[$name])) {
      $field_values[$name] = $meta[$name][0];
    }
  }
  
  // wp_nonce_field('ege_card_nonce', 'ege_card_nonce');
  ?>
  <table class="form-table">
    <?php foreach ($field_names as $name => $text): ?>
    <tr>
      <th> <label for="<?= $name; ?>"><?= $text; ?></label></th>
      <td>
        <input id="<?= $name; ?>"
         name="<?= $name; ?>"
         type="text"
         value="<?= esc_attr($field_values[$name]); ?>"
         style="width: 100%;"
         />
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php
}

function ege_cards_meta_box (WP_Post $post) {
  add_meta_box(
    'ege_card_meta',
    'Card Details',
    'ege_cards_post_meta_box_html'
  );
}

function ege_cards_post_title_placeholder ( $title ) {
  $screen = get_current_screen();

  if  ( 'ege_card' == $screen->post_type ) {
      $title = 'Enter Travel Card name here';
  }

  return $title;
}
add_filter( 'enter_title_here', 'ege_cards_post_title_placeholder');

function ege_cards_post_help_tab () {
    $screen = get_current_screen();

    if ( 'ege_card' != $screen->post_type )
        return;

    $args = [
        'id'      => 'ege_card',
        'title'   => 'Travel Cards Help',
        'content' => '<h3>Add/Edit Travel Card</h3><p>Enter the information below</p>',
    ];

    $screen->add_help_tab( $args );
}
add_action('admin_head', 'ege_cards_post_help_tab');

function ege_cards_post_updated_messages ($messages) {
  global $post, $post_ID;
  $link = esc_url( get_permalink($post_ID) );

  $messages['ege_card'] = array(
      0 => '',
      1 => sprintf( __('Card updated. <a href="%s">View card</a>'), $link ),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => __('Card updated.'),
      5 => isset($_GET['revision']) ? sprintf( __('Card restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
      6 => sprintf( __('Card published. <a href="%s">View card</a>'), $link ),
      7 => __('Card saved.'),
      8 => sprintf( __('Card submitted. <a target="_blank" href="%s">Preview card</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
      9 => sprintf( __('Card scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview card</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), $link ),
      10 => sprintf( __('Card draft updated. <a target="_blank" href="%s">Preview card</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
  return $messages;
}
add_filter( 'post_updated_messages', 'ege_cards_post_updated_messages');

function ege_cards_post_bulk_messages ( $bulk_messages, $bulk_counts ) {
  $bulk_messages['ege_card'] = array(
      'updated'   => _n( "%s card updated.", "%s cards updated.", $bulk_counts["updated"] ),
      'locked'    => _n( "%s card not updated, somebody is editing it.", "%s cards not updated, somebody is editing them.", $bulk_counts["locked"] ),
      'deleted'   => _n( "%s card permanently deleted.", "%s cards permanently deleted.", $bulk_counts["deleted"] ),
      'trashed'   => _n( "%s card moved to the Trash.", "%s cards moved to the Trash.", $bulk_counts["trashed"] ),
      'untrashed' => _n( "%s card restored from the Trash.", "%s cards restored from the Trash.", $bulk_counts["untrashed"] ),
  );

  return $bulk_messages;
}
add_filter( 'bulk_post_updated_messages', 'ege_cards_post_bulk_messages', 10, 2 );

function ege_cards_save_card ($post_id){
  global $ege_card_meta_names;
  $post = get_post($post_id);
  $is_revision = wp_is_post_revision($post_id);

  // Do not save meta for a revision or on autosave
  if ( $post->post_type != 'ege_card' || $is_revision )
      return;

  // Secure with nonce field check
  //if( ! check_admin_referer('ege_card_nonce', 'ege_card_nonce') )
  //    return;

  $field_names = $ege_card_meta_names;
  foreach ($field_names as $field_name => $text) {
    // Do not save meta if fields are not present,
    // like during a restore.
    if( !isset($_POST[$field_name]) )
        continue;

    // Clean up data
    $field_value = trim($_POST[$field_name]);
    // Do the saving and deleting
    if( ! (!isset( $field_value ) || $field_value === "") ) {
        update_post_meta($post_id, $field_name, $field_value);
    } else {
        delete_post_meta($post_id, $field_name);
    }
  }
}
add_action('save_post', 'ege_cards_save_card');