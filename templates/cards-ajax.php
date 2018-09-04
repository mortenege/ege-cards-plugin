<?php
/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php if (empty($posts)): ?>
  <div class="alert alert-info">No cards found with these criteria</div>
<?php else: ?>
<div class="ege-cards-grid">
<?php foreach ($posts as $post): ?>
  <?php
    $the_meta = get_post_meta($post->ID);
    $bonus_value = $the_meta['bonus_value'];
    $bonus_value = is_array($bonus_value) ? $bonus_value[0] : $bonus_value;
    $annual_fee = $the_meta['annual_fee'];
    $annual_fee = is_array($annual_fee) ? $annual_fee[0] : $annual_fee;
    $deep_link = $the_meta['deep_link'];
    $deep_link = is_array($deep_link) ? $deep_link[0] : $deep_link;
    $deep_link = $deep_link ? $deep_link : '#';
    $term_link = $the_meta['term_link'];
    $term_link = is_array($term_link) ? $term_link[0] : $term_link;
    $term_link = $term_link ? $term_link : '#';
  ?>
  <div class="ege-cards-col">
    <div class="ege-card">
      <a href="<?= $deep_link; ?>" target="_blank" class="ege-card-callout"><?= strtoupper($the_meta['callout'][0]); ?></a>
      <a href="<?= $deep_link; ?>" target="_blank" class="ege-card-image" style="background-image: url('<?= get_the_post_thumbnail_url($post); ?>');" >
      </a>
      <a href="<?= $deep_link; ?>" target="_blank" class="ege-card-title"><?= strtoupper($post->post_title); ?></a>
      <div class="ege-card-header">Bonus</div>
      <div class="ege-card-text"><?= $bonus_value; ?></div>
      <div class="ege-card-header">Annual Fee</div>
      <div class="ege-card-text"><?= $annual_fee; ?></div>
      <div style="padding-bottom: 100px;"></div>
      
      <div class="ege-card-footer">
        <a href="<?= $deep_link; ?>" target="_blank" class="btn ege-card-apply-btn">APPLY NOW</a>
        <a href="<?= $term_link; ?>" target="_blank" class="ege-card-terms-link">Terms and Conditions</a>
      </div>
    
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>