<?php
/*
Plugin Name:  Ege Cards
Plugin URI:   https://github.com/mortenege/ege-cards-plugin
Description:  Custom Created widget for SimpleFlying.com
Version:      20180831
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Set up Config and version number
$ege_cards_config = [
  'version' => '20180831'
];

// Setup meta fields to be stored for each card
// + default POST field
$ege_card_meta_names = [
  'callout' => 'Callout text',
  'issuer' => 'Bank/Issuer name',
  'deep_link' => 'Deep Link',
  'term_link' => 'Link to Terms',
  'annual_fee' => 'Annual Fee text',
  'bonus_value' => 'Bonus value text'
];

/**
 * Create Custom Post type: travelcard
 */
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

  register_post_type( 'travelcard',
    array(
      'rewrite' => ['slug' => 'travelcard'],
      'taxonomies' => array('card_category', 'card_tag'),
      'register_meta_box_cb' => 'ege_cards_meta_box',
      'labels' => $labels,
      'public' => true,
      'has_archive' => false,
      'menu_icon' => 'dashicons-id',
      'supports' => $supports
    )
  );
}
add_action( 'init', 'ege_cards_create_post_type' );

/**
 * Custom meta box HTML
 * @param  WP_Post $post
 */
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

/**
 * Add Meta Box
 * @param  WP_Post $post
 */
function ege_cards_meta_box (WP_Post $post) {
  add_meta_box(
    'ege_card_meta',
    'Card Details',
    'ege_cards_post_meta_box_html'
  );
}

/**
 * Filter POST title placeholder
 * @param  String $title
 * @return String
 */
function ege_cards_post_title_placeholder ( $title ) {
  $screen = get_current_screen();

  if  ( 'travelcard' == $screen->post_type ) {
      $title = 'Enter Travel Card name here';
  }

  return $title;
}
add_filter( 'enter_title_here', 'ege_cards_post_title_placeholder');

/**
 * Create POST "help" tab
 */
function ege_cards_post_help_tab () {
    $screen = get_current_screen();

    if ( 'travelcard' != $screen->post_type )
        return;

    $args = [
        'id'      => 'travelcard',
        'title'   => 'Travel Cards Help',
        'content' => file_get_contents(__DIR__ . '/templates/help.php'),
    ];

    $screen->add_help_tab( $args );
}
add_action('admin_head', 'ege_cards_post_help_tab');

/**
 * Set all messages related to updating travelcard
 * @param  Array $messages [description]
 * @return Array
 */
function ege_cards_post_updated_messages ($messages) {
  global $post, $post_ID;
  $link = esc_url( get_permalink($post_ID) );

  $messages['travelcard'] = array(
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

/**
 * Set all bulk update messages for travelcard
 * @param  Array $bulk_messages
 * @param  [type] $bulk_counts   [description]
 * @return Array
 */
function ege_cards_post_bulk_messages ( $bulk_messages, $bulk_counts ) {
  $bulk_messages['travelcard'] = array(
      'updated'   => _n( "%s card updated.", "%s cards updated.", $bulk_counts["updated"] ),
      'locked'    => _n( "%s card not updated, somebody is editing it.", "%s cards not updated, somebody is editing them.", $bulk_counts["locked"] ),
      'deleted'   => _n( "%s card permanently deleted.", "%s cards permanently deleted.", $bulk_counts["deleted"] ),
      'trashed'   => _n( "%s card moved to the Trash.", "%s cards moved to the Trash.", $bulk_counts["trashed"] ),
      'untrashed' => _n( "%s card restored from the Trash.", "%s cards restored from the Trash.", $bulk_counts["untrashed"] ),
  );

  return $bulk_messages;
}
add_filter( 'bulk_post_updated_messages', 'ege_cards_post_bulk_messages', 10, 2 );

/**
 * 'Save POST' hook
 * @param  Integer $post_id
 */
function ege_cards_save_card ($post_id){
  global $ege_card_meta_names;
  $post = get_post($post_id);
  $is_revision = wp_is_post_revision($post_id);

  // Do not save meta for a revision or on autosave
  if ( $post->post_type != 'travelcard' || $is_revision )
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

/**
 * The widget shortcode
 * @param  array  $atts    [description]
 * @param  string $content [description]
 * @param  string $tag     [description]
 * @return [type]          [description]
 */
function ege_cards_basic_shortcode($atts = [], $content = '', $tag = ''){
  // bootstrap  
  wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
  wp_enqueue_script( 'bootstrap','https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array( 'jquery' ),'',true );

  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);
  // override default attributes with user attributes
  $parsed_atts = shortcode_atts([], $atts, $tag);

  $filename = '/templates/cards.php';

  ob_start();
  require_once(dirname(__FILE__) . $filename);
  return ob_get_clean();   
}
add_shortcode( 'ege_cards', 'ege_cards_basic_shortcode');

/**
 * Register custom taxonomies
 */
function ege_cards_create_taxonomies() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => _x( 'Card Categories', 'taxonomy general name', 'textdomain' ),
    'singular_name'     => _x( 'Card Category', 'taxonomy singular name', 'textdomain' ),
    'search_items'      => __( 'Search Card Categories', 'textdomain' ),
    'all_items'         => __( 'All Card Categories', 'textdomain' ),
    'parent_item'       => __( 'Parent Card Category', 'textdomain' ),
    'parent_item_colon' => __( 'Parent Card Category:', 'textdomain' ),
    'edit_item'         => __( 'Edit Card Category', 'textdomain' ),
    'update_item'       => __( 'Update Card Category', 'textdomain' ),
    'add_new_item'      => __( 'Add New Card Category', 'textdomain' ),
    'new_item_name'     => __( 'New Card Category Name', 'textdomain' ),
    'menu_name'         => __( 'Card Category', 'textdomain' ),
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'card_category' ),
  );

  register_taxonomy( 'card_category', array( 'travelcard' ), $args );

  // Add 'card tags' taxonomy
  $labels = array(
    'name'                       => _x( 'Card Tags', 'taxonomy general name', 'textdomain' ),
    'singular_name'              => _x( 'Card Tag', 'taxonomy singular name', 'textdomain' ),
    'search_items'               => __( 'Search Card Tags', 'textdomain' ),
    'popular_items'              => __( 'Popular Card Tags', 'textdomain' ),
    'all_items'                  => __( 'All Card Tags', 'textdomain' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Card Tag', 'textdomain' ),
    'update_item'                => __( 'Update Card Tag', 'textdomain' ),
    'add_new_item'               => __( 'Add New Card Tag', 'textdomain' ),
    'new_item_name'              => __( 'New Card Tag Name', 'textdomain' ),
    'separate_items_with_commas' => __( 'Separate card tags with commas', 'textdomain' ),
    'add_or_remove_items'        => __( 'Add or remove card tags', 'textdomain' ),
    'choose_from_most_used'      => __( 'Choose from the most used card tags', 'textdomain' ),
    'not_found'                  => __( 'No card tags found.', 'textdomain' ),
    'menu_name'                  => __( 'Card Tags', 'textdomain' ),
  );

  $args = array(
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'card_tag' ),
  );

  register_taxonomy( 'card_tag', 'travelcard', $args );
}
add_action( 'init', 'ege_cards_create_taxonomies', 0 );

/**
 * AJAX call to fetch cards
 * GET parameters: search, category, tag
 */
function ege_cards_search_cards () {
  $args = [
      'post_type' => 'travelcard',
      'post_status' => 'publish',
      'numberposts' => -1
  ];

  $category = isset($_GET['category']) ? $_GET['category'] : null;
  $tag = isset($_GET['tag']) ? $_GET['tag'] : null;
  if ($tag && $category) {
    $args['tax_query'] = array(
      'relation' => 'AND',
      array(
          'taxonomy' => 'card_category',
          'field' => 'slug',
          'terms' => $category,
      ),
      array(
          'taxonomy' => 'card_tag',
          'field' => 'slug',
          'terms' => $tag,
      )
    );
  } elseif ($tag) {
    
    $args['tax_query'] = array(
      array(
          'taxonomy' => 'card_tag',
          'field' => 'slug',
          'terms' => $tag,
      )
    );
  } elseif ($category) {
    $args['tax_query'] = array(
      array(
          'taxonomy' => 'card_category',
          'field' => 'slug',
          'terms' => $category,
      )
    );
  }
  $search = isset($_GET['search']) ? $_GET['search'] : null;
  if ($search) {
    $args['s'] = $search;
  }

  $posts  = get_posts($args);
  
  include __DIR__ . '/templates/cards-ajax.php';
  wp_die();
}
add_action('wp_ajax_nopriv_ege_cards_search_cards','ege_cards_search_cards');
add_action('wp_ajax_ege_cards_search_cards','ege_cards_search_cards');

/**
 * Add hack to hide other meta boxes
 * @param  Array      $hidden
 * @param  WP_Screen  $screen
 * @param  Bool       $use_defaults
 * @return Array
 */
function ege_cards_filter_hidden_boxes ($hidden, $screen, $use_defaults) {
  global $wp_meta_boxes;
  $cpt = 'travelcard'; // Modify this to your needs!
  $keep = array('postimagediv', 'tagsdiv-card_tag', 'card_categorydiv', 'submitdiv', 'ege_card_meta');
  if( $cpt === $screen->id && isset( $wp_meta_boxes[$cpt] ) ){
    $tmp = array();
    foreach( (array) $wp_meta_boxes[$cpt] as $context_key => $context_item ){
      foreach( $context_item as $priority_key => $priority_item ){
        foreach( $priority_item as $metabox_key => $metabox_item )
          if (!in_array($metabox_key, $keep)) {
            $tmp[] = $metabox_key;
          }
      }
    }
    $hidden = $tmp;  // Override the current user option here.
  }
  return $hidden;
}
add_filter( 'hidden_meta_boxes', 'ege_cards_filter_hidden_boxes', 10, 3 );