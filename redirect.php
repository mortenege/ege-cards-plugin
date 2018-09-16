<?php 
define('WP_USE_THEMES', false);
require('../../../wp-load.php');
if (!isset($_GET['url'])) {
  global $wp_query;
  $wp_query->set_404();
  status_header(404);
  nocache_headers();
  require get_404_template();
  wp_die();
}

// Count the occurrence of this click
if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
  $val = get_post_meta($_GET['id'], 'link_click_cnt', true);
  $val = preg_match('/^[0-9]+$/', $val) ? $val : 0;
  $val++;
  update_post_meta($_GET['id'], 'link_click_cnt', $val);
}

wp_redirect($_GET['url']);
die();