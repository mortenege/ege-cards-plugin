<?php
/*
Plugin Name:  Ege Cards
Plugin URI:   https://github.com/mortenege/ege-cards-plugin
Description:  Custom Created widget for SimpleFlying.com
Version:      20181113
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EgeCardsPlugin {
  const VERSION = '20181113';

  const META = array(
    'callout' => 'Table Top Callout',
    'long_title' => 'Below Article Widget Title',
    'deep_link' => 'Widget Deeplink',
    'deep_link_2' => 'Article Deeplink',
    'text_link' => 'Use text link',
    'term_link' => 'Link to Terms',
    'annual_fee' => 'Annual Fee Information',
    'bonus_value' => 'Table Bonus Text',
    'official_name_1' => 'Compliance Name #1',
    'official_name_2' => 'Compliance Name #2',
    'official_name_3' => 'Compliance Name #3',
    'official_name_4' => 'Compliance Name #4',
    'official_name_5' => 'Compliance Name #5'
  );

  public function __construct(){
    // De- and Register hooks
    register_activation_hook( __FILE__, [self::class, 'activatePlugin'] );
    register_deactivation_hook( __FILE__, [self::class, 'deactivatePlugin'] );

    // Init hook
    add_action( 'init', [self::class, 'createPostType'] );
    add_action( 'init', [self::class, 'createTaxonomies'], 0 );
    add_action( 'init', [self::class, 'addCustomRewriteRule'] );
    add_action( 'admin_init', [self::class, 'registerSettings'] );
    add_action( 'admin_menu', [self::class, 'addMenu'] );

    // Shortcodes
    add_shortcode( 'ege_cards', [self::class, 'basicShortcode'] );
    add_shortcode( 'ege_cards_sticky_card', [self::class, 'stickyWidgetShortcode'] );
    add_shortcode( 'ege_cards_link', [self::class, 'linkShortcode'] );
    add_shortcode( 'ege_cards_disclaimer', [self::class, 'disclaimerShortcode'] );
    add_shortcode( 'ege_cards_related', [self::class, 'relatedShortcode'] );

    // AJAX
    add_action( 'wp_ajax_nopriv_ege_cards_search_cards', [self::class, 'ajaxSearchCards'] );
    add_action( 'wp_ajax_ege_cards_search_cards', [self::class, 'ajaxSearchCards'] );
    add_action( 'wp_ajax_ege_cards_make_sticky', [self::class, 'ajaxMakeSticky'] );
    add_action( 'wp_ajax_ege_cards_save_related_links', [self::class, 'saveRelatedLinks']);

    // Custom POST title filter
    add_filter( 'enter_title_here', [self::class, 'postTitlePlaceholder'] );

    // Custom POST help tab
    add_action( 'admin_head', [self::class, 'postHelpTab'] );

    // Custom POST updated messages
    add_filter( 'post_updated_messages', [self::class, 'postUpdatedMessages'] );
    add_filter( 'bulk_post_updated_messages', [self::class, 'postBulkMessages'], 10, 2 );

    // Custom POST save
    add_action('save_post', [self::class, 'saveCard']);

    // Hack to remove uncessecary meta boxes
    add_filter( 'hidden_meta_boxes', [self::class, 'filterHiddenBoxes'], 10, 3 );

    // Add button to POST edit text area
    add_action('media_buttons', [self::class, 'addCustomLinkButton'], 15);
    
    // Add disclaimer metabox
    add_action( 'add_meta_boxes', array(self::class, 'addDisclaimerMetaBox' ), 1000);
    add_action( 'save_post', [self::class, 'savePostMeta']);

    // Enqueue admin sctipts
    add_action( 'admin_enqueue_scripts', [self::class, 'addAdminScripts'], 10, 1 );

    // Add extra column tp travelcards table view
    add_filter( 'manage_travelcard_posts_columns' , [self::class, 'addColumns'] );
    add_action( 'manage_posts_custom_column' , [self::class, 'customColumns'], 10, 2 );
  }

  public static function activatePlugin(){
    self::addCapabilities();
  }

  public static function deactivatePlugin(){
    self::removeCapabilities();
  }

  private static function addCapabilities() {
    $admin_role = get_role('administrator');
    $admin_role->add_cap('read_travelcard');
    $admin_role->add_cap('edit_travelcard');
    $admin_role->add_cap('delete_travelcard');
    $admin_role->add_cap('edit_travelcards');
    $admin_role->add_cap('edit_others_travelcards');
    $admin_role->add_cap('publish_travelcards');
    $admin_role->add_cap('read_private_travelcards');

    $editor_role = get_role('editor');
    $editor_role->add_cap('read_travelcard');
    $editor_role->add_cap('edit_travelcard');
    $editor_role->add_cap('delete_travelcard');
    $editor_role->add_cap('edit_travelcards');
    $editor_role->add_cap('edit_others_travelcards');
    $editor_role->add_cap('publish_travelcards');
    $editor_role->add_cap('read_private_travelcards');

    $author_role = get_role('author');
    $author_role->add_cap('read_travelcard');
    $author_role->add_cap('edit_travelcard', false);
    $author_role->add_cap('delete_travelcard', false);
    $author_role->add_cap('edit_travelcards', false);
    $author_role->add_cap('edit_others_travelcards', false);
    $author_role->add_cap('publish_travelcards', false);
    $author_role->add_cap('read_private_travelcards', false);
  }

  private static function removeCapabilities() {
    $admin_role = get_role('administrator');
    $admin_role->add_cap('read_travelcard');
    $admin_role->add_cap('edit_travelcard', false);
    $admin_role->add_cap('delete_travelcard', false);
    $admin_role->add_cap('edit_travelcards', false);
    $admin_role->add_cap('edit_others_travelcards', false);
    $admin_role->add_cap('publish_travelcards', false);
    $admin_role->add_cap('read_private_travelcards', false);

    $editor_role = get_role('editor');
    $editor_role->add_cap('read_travelcard');
    $editor_role->add_cap('edit_travelcard', false);
    $editor_role->add_cap('delete_travelcard', false);
    $editor_role->add_cap('edit_travelcards', false);
    $editor_role->add_cap('edit_others_travelcards', false);
    $editor_role->add_cap('publish_travelcards', false);
    $editor_role->add_cap('read_private_travelcards', false);

    $author_role = get_role('author');
    $author_role->add_cap('read_travelcard');
    $author_role->add_cap('edit_travelcard', false);
    $author_role->add_cap('delete_travelcard', false);
    $author_role->add_cap('edit_travelcards', false);
    $author_role->add_cap('edit_others_travelcards', false);
    $author_role->add_cap('publish_travelcards', false);
    $author_role->add_cap('read_private_travelcards', false);
  }

  /**
   * Create Custom Post type: travelcard
   */
  public static function createPostType() {
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

    $supports = ['title', 'editor', 'thumbnail', 'excerpt'];

    register_post_type( 'travelcard',
      array(
        'rewrite' => ['slug' => 'travelcard'],
        'taxonomies' => array('card_category', 'card_tag'),
        'register_meta_box_cb' => [self::class, 'addMetaBox'],
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-id',
        'supports' => $supports,
        'capability_type' => array('travelcard', 'travelcards'),
        'map_meta_cap' => false,
      )
    );
  }

  /**
   * Custom meta box HTML
   * @param  WP_Post $post
   */
  public static function postMetaBoxHtml ($post) {
    $field_names = self::META;
    $meta = get_post_meta($post->ID);

    $field_values = [];
    foreach ($field_names as $name => $text) {
      if (isset($meta[$name])) {
        $field_values[$name] = $meta[$name][0];
      }
    }
    
    // wp_nonce_field('ege_card_nonce', 'ege_card_nonce');
    $sticky_id = get_option('ege_cards_sticky_id', 0);
    $is_sticky = $post->ID === $sticky_id ? '1' : '0';
    ?>
    <table class="form-table">
      <?php foreach ($field_names as $name => $text): ?>
      <tr>
        <th> <label for="<?= $name; ?>"><?= $text; ?></label></th>
        <td>
          <?php if ($name !== 'text_link'): ?>
          <input id="<?= $name; ?>"
           name="<?= $name; ?>"
           type="text"
           value="<?= esc_attr($field_values[$name]); ?>"
           style="width: 100%;"
           />
          <?php else: ?>
          <input id="<?= $name; ?>"
           name="<?= $name; ?>"
           type="checkbox"
           value="1"
           <?= checked($field_values[$name]); ?>
           />
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    
    <hr style="margin: 20px 0;"/>

    <div data-is-sticky="<?= $is_sticky; ?>">
      <div class="show-if-sticky"><strong>This card has been selected as <em>Sticky</em></strong></div>
      <button class="hide-if-sticky" id="ege-cards-admin-make-sticky-btn">Make me sticky</button>
    </div>
    <style>
      div[data-is-sticky="0"] .show-if-sticky {
        display: none;
      }
      div[data-is-sticky="1"] .hide-if-sticky {
        display: none;
      }
    </style>
    <script>
      jQuery(document).ready(function($){
        var $btn = $('#ege-cards-admin-make-sticky-btn').first();
        $btn.click(function(e){
          $btn.attr('disabled', 'disabled');
          e.preventDefault();
          var data = {
            action: 'ege_cards_make_sticky',
            id: '<?= $post->ID; ?>'
          }
          $.post(ajaxurl, data, function(response, status){
            $btn.removeAttr('disabled');
            if (response.error) {
              alert(response.error);
            } else {
              $btn.parent().attr('data-is-sticky', '1');
            }
          });
        });
      });
    </script>
    <?php
  }

  /**
   * Add Meta Box
   * @param  WP_Post $post
   */
  public static function addMetaBox (WP_Post $post) {
    add_meta_box(
      'ege_card_meta',
      'Card Details',
      [self::class, 'postMetaBoxHtml']
    );
  }

  public static function addDisclaimerMetaBox () {
    add_meta_box(
      'ege_cards_disclaimer_mb',
      'Travelcard Disclaimer Top',
      [self::class, 'mbDisclaimerHtml'],
      ['post', 'page'],
      'side',
      'high'
    );  
  }

  public static function mbDisclaimerHtml (WP_Post $post) {
    $value = get_post_meta($post->ID, 'ege_cards_disclaimer', true);
    ?>
    <label for="ege_cards_disclaimer">
      <input type="checkbox" id="ege_cards_disclaimer" name="ege_cards_disclaimer" value="1" <?php checked($value); ?> />
      Add Disclaimer to the top of this post
    </label>
    <?php
  }

  /**
   * Change the placeholder of the POST edit title
   * @param  String $title
   * @return String
   */
  public static function postTitlePlaceholder ( $title ) {
    $screen = get_current_screen();

    if  ( 'travelcard' == $screen->post_type ) {
        $title = 'Enter Travel Card name here';
    }

    return $title;
  }

  /**
   * Create Custom POST "help" tab
   */
  public static function postHelpTab () {
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

  /**
   * Set all messages related to updating travelcard
   * @param  Array $messages [description]
   * @return Array
   */
  public static function postUpdatedMessages ($messages) {
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

  /**
   * Set all bulk update messages for travelcard
   * @param  Array $bulk_messages
   * @param  [type] $bulk_counts   [description]
   * @return Array
   */
  public static function postBulkMessages ( $bulk_messages, $bulk_counts ) {
    $bulk_messages['travelcard'] = array(
        'updated'   => _n( "%s card updated.", "%s cards updated.", $bulk_counts["updated"] ),
        'locked'    => _n( "%s card not updated, somebody is editing it.", "%s cards not updated, somebody is editing them.", $bulk_counts["locked"] ),
        'deleted'   => _n( "%s card permanently deleted.", "%s cards permanently deleted.", $bulk_counts["deleted"] ),
        'trashed'   => _n( "%s card moved to the Trash.", "%s cards moved to the Trash.", $bulk_counts["trashed"] ),
        'untrashed' => _n( "%s card restored from the Trash.", "%s cards restored from the Trash.", $bulk_counts["untrashed"] ),
    );

    return $bulk_messages;
  }

  public static function savePostMeta ($post_id) {
    if (array_key_exists('ege_cards_disclaimer', $_POST)) {
      update_post_meta(
        $post_id,
        'ege_cards_disclaimer',
        $_POST['ege_cards_disclaimer']
      );
    } else {
      delete_post_meta($post_id, 'ege_cards_disclaimer');
    }
  }

  /**
   * 'Save POST' hook
   * @param  Integer $post_id
   */
  public static function saveCard ($post_id){
    $post = get_post($post_id);
    $is_revision = wp_is_post_revision($post_id);

    // Do not save meta for a revision or on autosave
    if ( $post->post_type != 'travelcard' || $is_revision )
        return;

    // Secure with nonce field check
    //if( ! check_admin_referer('ege_card_nonce', 'ege_card_nonce') )
    //    return;
    //    
    if (!array_key_exists('text_link', $_POST)){
        delete_post_meta($post_id, 'text_link');
    }

    $field_names = self::META;
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

  public static function relatedShortcode ($atts = []) {
    
    wp_enqueue_style(
      'ege_cards',
      plugin_dir_url( __FILE__ ) . 'static/style.css',
      null, // No dependencies
      self::VERSION // Cache buster
    );

    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $parsed_atts = shortcode_atts([
      'id' => '0'
    ], $atts, $tag);

    if (!preg_match('/^[0-9]+$/', $parsed_atts['id'])) {
      $id = 0;
    } else {
      $id = intval($parsed_atts['id']);
    }

    $links = get_option('ege_cards_related_links', []);

    $index = null;

    foreach ($links as $i => $link) {
      if ($link->id == $id) {
        $index = $i;
        break;
      }
    }

    if ($index !== null) {
      $link = $links[$index];
    } elseif (count($links) > 0) {
      $link = $links[0];
    } else {
      return '';
    }
    
    ob_start();
    ?>
    <div class="ege-cards-related" style="background-image: url('<?= plugins_url('ege-cards-plugin/static/sf-logo-sm.png'); ?>')">
      <span class="ege-cards-related--title"><?= $link->title; ?></span>
      <a href="<?= $link->link; ?>"><?= $link->link_text; ?></a>
    </div>
    <?php

    return ob_get_clean();
  }

  public static function disclaimerShortcode ($atts = []) {
    $post_has_disclaimer = get_post_meta(get_the_ID(), 'ege_cards_disclaimer', true);
    // if (!$post_has_disclaimer) return '';
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    // override default attributes with user attributes
    $parsed_atts = shortcode_atts([
      'type' => 'top'
    ], $atts, $tag);

    if ($parsed_atts['type'] === 'top') {
      if (!$post_has_disclaimer) return '';
      $value = get_option('ege_cards_disclaimer_1', '');
    } elseif ($parsed_atts['type'] === 'bottom') {
      $value = get_option('ege_cards_disclaimer_2', '');
    }

    return '<p class="ege-disclaimer">'.$value.'</p>';

  }

  /**
   * The widget shortcode
   * @param  array  $atts    [description]
   * @param  string $content [description]
   * @param  string $tag     [description]
   * @return [type]          [description]
   */
  public static function basicShortcode($atts = [], $content = '', $tag = ''){
    wp_enqueue_style(
      'ege_cards',
      plugin_dir_url( __FILE__ ) . 'static/style.css',
      null, // No dependencies
      self::VERSION // Cache buster
    );

    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    // override default attributes with user attributes
    $parsed_atts = shortcode_atts([], $atts, $tag);

    $filename = '/templates/cards.php';

    ob_start();
    require_once(dirname(__FILE__) . $filename);
    return ob_get_clean();
  }

  /**
   * Create Custom Taxonomies for Travelcard
   */
  public static function createTaxonomies() {
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
      'capabilities'      => array(
        'manage_terms' => 'edit_travelcards',
        'edit_terms'   => 'edit_travelcards',
        'delete_terms' => 'edit_travelcards',
        'assign_terms' => 'edit_travelcards'
      )
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
      'capabilities'      => array(
        'manage_terms' => 'edit_travelcards',
        'edit_terms'   => 'edit_travelcards',
        'delete_terms' => 'edit_travelcards',
        'assign_terms' => 'edit_travelcards'
      )
    );

    register_taxonomy( 'card_tag', 'travelcard', $args );
  }

  /**
   * AJAX call to fetch cards
   * GET parameters: search, category, tag
   */
  public static function ajaxSearchCards () {
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

  /**
   * Add hack to hide other meta boxes
   * @param  Array      $hidden
   * @param  WP_Screen  $screen
   * @param  Bool       $use_defaults
   * @return Array
   */
  public static function filterHiddenBoxes ($hidden, $screen, $use_defaults) {
    global $wp_meta_boxes;
    $cpt = 'travelcard';
    $keep = array('postexcerpt', 'postimagediv', 'tagsdiv-card_tag', 'card_categorydiv', 'submitdiv', 'ege_card_meta');
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

  /**
   * Display 'sticky' travelcard
   */
  public static function stickyWidgetShortcode ($atts = [], $content = '', $tag = ''){
    wp_enqueue_style(
      'ege_cards',
      plugin_dir_url( __FILE__ ) . 'static/style.css',
      null, // No depend
      self::VERSION // Cache buster
    );

    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    // override default attributes with user attributes
    $parsed_atts = shortcode_atts([
      'short' => false
    ], $atts, $tag);

    $parsed_atts['short'] = $parsed_atts['short'] === 'true' ? true : false;
    $parsed_atts['short'] = (bool) $parsed_atts['short'];
    $sticky_params_short = $parsed_atts['short'];

    $filename = '/templates/sticky-card.php';
    
    $sticky_id = get_option('ege_cards_sticky_id', 0);
    if (!$sticky_id) {
      $args = [
        'post_type' => 'travelcard',
        'post_status' => 'publish',
        'numberposts' => 1
      ];
      $sticky_card = get_posts($args);
      $sticky_card = $sticky_card[0];
    } else {
      $sticky_card = get_post($sticky_id);
    }
    
    ob_start();
    require_once(dirname(__FILE__) . $filename);
    return ob_get_clean();   
  }

  /**
   * AJAX call to make this travelcard 'sticky'
   */
  public static function ajaxMakeSticky () {
    $data = array();
    if (!isset($_POST['id'])){
      $data['error'] = 'No Card ID!';
    } elseif (!preg_match('/^[0-9]+$/', $_POST['id'])) {
      $data['error'] = 'Invalid Card ID';
    } else {
      update_option('ege_cards_sticky_id', $_POST['id']);
      $data['status'] = 'success';
    }
    wp_send_json($data);
  }

  public static function sanitizeBoolean( $input ){
    return isset( $input ) ? true : false;
  }

  public static function useRedirectHtmlCallback(){
    $value = get_option('ege_cards_use_redirect', false);
    ?>
    <input type="checkbox" name="ege_cards_use_redirect" value="1" <?php checked($value); ?>" />
    <?php
  }

  public static function settingsHtmlCallback(){
    ?>
    <p>These are settings for the Travelcards plugin</p>
    <?php
  }

  public static function disclaimerTextareaHtml ($name) {
    $value = get_option($name, '');
    ?>
    <textarea name="<?= $name; ?>" style="width:100%;height:100px;"><?= $value; ?></textarea>
    <?php
  }

  public static function registerSettings () {
    register_setting( 'general', 'ege_cards_use_redirect', [self::class, 'sanitizeBoolean'] );

    add_settings_section(
      'ege_cards_settings', // ID
      'Travelcards Settings', // Section title
      [self::class, 'settingsHtmlCallback'], // Callback for your function
      //'general' // Location (Settings > General)
      'sf_settings'
    );

    add_settings_field(
      'ege_cards_use_redirect',
      'Enable Travelcard click counting',
      [self::class, 'useRedirectHtmlCallback'],
      'sf_settings',
      'ege_cards_settings'
    );

    register_setting( 'sf_settings', 'ege_cards_disclaimer_1' );
    add_settings_field(
      'ege_cards_disclaimer_1',
      'Disclaimer top',
      function () { self::disclaimerTextareaHtml('ege_cards_disclaimer_1'); },
      'sf_settings',
      'ege_cards_settings'
    );

    register_setting( 'sf_settings', 'ege_cards_disclaimer_2' );
    add_settings_field(
      'ege_cards_disclaimer_2',
      'Disclaimer bottom',
      function () { self::disclaimerTextareaHtml('ege_cards_disclaimer_2'); },
      'sf_settings',
      'ege_cards_settings'
    );
  }

  /**
   * https://codex.wordpress.org/Javascript_Reference/ThickBox
   */
  public static function addCustomLinkButton() {
    global $wpdb;
    $results = $wpdb->get_results( "SELECT post.ID, post.post_title, meta1.meta_value as m1, meta2.meta_value as m2, meta3.meta_value as m3, meta4.meta_value as m4, meta5.meta_value as m5 FROM {$wpdb->prefix}posts as post LEFT JOIN {$wpdb->prefix}postmeta AS meta1 ON post.ID = meta1.post_id AND meta1.meta_key = 'official_name_1' LEFT JOIN {$wpdb->prefix}postmeta AS meta2 ON post.ID = meta2.post_id AND meta2.meta_key = 'official_name_2' LEFT JOIN {$wpdb->prefix}postmeta AS meta3 ON post.ID = meta3.post_id AND meta3.meta_key = 'official_name_3' LEFT JOIN {$wpdb->prefix}postmeta AS meta4 ON post.ID = meta4.post_id AND meta4.meta_key = 'official_name_4' LEFT JOIN {$wpdb->prefix}postmeta AS meta5 ON post.ID = meta5.post_id AND meta5.meta_key = 'official_name_5' WHERE post.post_type = 'travelcard' AND post.post_status = 'publish'", OBJECT );

    include __DIR__ . '/templates/card-link-inserter.php';

    // Related links
    $links = get_option('ege_cards_related_links', []);
    ?>
    <div id="ege-cards-related-link-manager" style="display:none;">
      <p>Insert a 'Related' Link</p>
      <select id="ege-cards-related-inserter" style="width:100%">
        <?php foreach ($links as $link): ?>
          <option value="<?= $link->id; ?>">[<?= $link->id; ?>] <?= $link->title; ?> <?= $link->link_text; ?></option>
        <?php endforeach; ?>
      </select>
      <button class="button" id="ege-cards-related-btn">Choose</button>
    </div>
    <a href="#TB_inline?width=400&height=200&inlineId=ege-cards-related-link-manager" class="button thickbox">Insert Related Link</a>
    <?php
  }

  /**
   * Add shortcode for in-post deep link
   */
  public static function linkShortcode($atts = [], $content = '', $tag = ''){
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    // override default attributes with user attributes
    $parsed_atts = shortcode_atts([
      'id' => null,
      'caption' => 'official_name_1'
    ], $atts, $tag);

    // get the ID from shortcode atts
    $id = $parsed_atts['id'];
    if (!$id) return '<a href="#">[incorrect ID]</a>';

    // find card in database
    $card = get_post($id);
    // make sure POST is a 'travelcard'
    if (!$card || $card->post_type !== 'travelcard') return '<a href="#">[not found]</a>';
    // retrieve meta for card
    $meta = get_post_meta($card->ID);

    // set caption
    $caption = $parsed_atts['caption'];
    if (preg_match('/^official\_name\_[0-5]$/', $caption)) {
      $caption = $caption ? $meta[$caption] : null;
      $caption = $caption ? $caption[0] : $card->post_title;
    }

    // set url
    $url = $meta['deep_link_2'];
    $url = $url ? $url[0] : '#';
    
    // build HTML
    $url = self::createRedirectUrl($url, $id);
    return '<a href="' . $url . '" class="ege-cards-link">' . $caption . '</a>';
  }

  /**
   * Remember to 'flush' rewrite rules upon changes
   * >> settings->permalinks->save (without making changes)
   */
  public static function addCustomRewriteRule() {
    add_rewrite_rule(
      '^travelcard_redirect',
      'wp-content/plugins/ege-cards-plugin/redirect.php',
      'top'
    );
  }

  public static function addAdminScripts(){
    wp_enqueue_script(
      'ege_cards_admin',
      plugin_dir_url( __FILE__ ) . 'static/admin.js',
      null,
      self::VERSION
    );
  }

  public static function addColumns($columns) {
    // unset($columns['author']);
    return array_merge($columns, 
      array(
        'page_displays' => 'Page Displays',
        'click_count' => 'Click Count'
      )
    );
  }

  public static function customColumns( $column, $post_id ) {
    global $wpdb; // {$wpdb->prefix}
    switch ( $column ) {
      case 'click_count':
        $val = get_post_meta($post_id, 'link_click_cnt', true);
        $val = preg_match('/^[0-9]+$/', $val) ? $val : 0;
        echo $val;
        break;
      case 'page_displays':
        $likeStr = '%[ege\_cards\_link_id="' . $post_id . '"%';
        $count = $wpdb->get_var( "SELECT COUNT(*) as count FROM wp_posts WHERE post_type IN ('post', 'page') AND post_status = 'publish' AND post_content LIKE '" . $likeStr . "';" );
        echo $count;
        break;
    }
  }

  public static function createRedirectUrl($url, $id){
    if (get_option('ege_cards_use_redirect', false)) {
      return get_site_url() . '/travelcard_redirect?url=' . urlencode($url) . '&id=' . $id;
    }
    return $url;
  }

   public static function settingsPageHtmlCallback() {
    ?>

    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
      <?php
        settings_fields( 'sf_settings' );
        do_settings_sections( 'sf_settings' );
        submit_button( 'Save Settings' );
      ?>
      </form>
    </div>
    <?php 
    $filename = '/templates/related-links.php';
    include dirname(__FILE__) . $filename;
  }

  public static function addMenu() {
    add_submenu_page(
      'edit.php?post_type=travelcard',
      'SF Settings',
      'SF Settings',
      'manage_options',
      'sf_settings',
      [self::class, 'settingsPageHtmlCallback']
    );
  }

  public static function saveRelatedLinks () {
    $links = $_POST['links'];
    if (!isset($links)) wp_send_json_error('Missing property', 400);
    
    $links = stripslashes($links);
    $links = json_decode($links);

    if (!is_array($links)) {
      wp_send_json_error('Expected array, got ' . gettype($links), 400);  
    }
    
    update_option('ege_cards_related_links', $links);
    
    wp_send_json($links);
  }
}

new EgeCardsPlugin();