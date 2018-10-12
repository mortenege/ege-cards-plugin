<?php 
$sticky_card_meta = get_post_meta($sticky_card->ID);
$sticky_card_title = $sticky_card_meta['long_title'];
$sticky_card_title = is_array($sticky_card_title) ? $sticky_card_title[0] : $sticky_card_title;
$sticky_card_title = $sticky_card_title ? $sticky_card_title : $sticky_card->post_title;

$sticky_card_deep_link = $sticky_card_meta['deep_link'];
$sticky_card_deep_link = is_array($sticky_card_deep_link) ? $sticky_card_deep_link[0] : $sticky_card_deep_link;
$sticky_card_deep_link = EgeCardsPLugin::createRedirectUrl($sticky_card_deep_link, $sticky_card->ID);
$sticky_card_deep_link = $sticky_card_deep_link ? $sticky_card_deep_link : '#';

$use_text_link = $sticky_card_meta['text_link'];
$use_text_link = $use_text_link ? true : false;
?>
<div id="ege-sticky-card">
  
  <a href="<?= $sticky_card_deep_link; ?>" target="_blank" class="ege-sticky-title"><?= $sticky_card_title; ?></a>
  
  <div class="ege-sticky-table">
    <div class="ege-sticky-excerpt"><?= $sticky_card->post_excerpt; ?></div>
    <div class="ege-sticky-travelcard-container">
      <a href="<?= $sticky_card_deep_link; ?>" target="_blank" class="ege-sticky-travelcard" style="background-image: url('<?= get_the_post_thumbnail_url($sticky_card); ?>');"></a>
    </div>
  </div>
  
  
  <?php if ($use_text_link): ?>
  <div style="margin-bottom:10px;text-align:center;">
    <a href="<?= $sticky_card_deep_link; ?>" target="_blank" style="text-decoration: underline;font-size:1.2em">Learn more about this card</a>
  </div>
  <?php else: ?>
  <div style="margin-bottom:10px;">
    <a href="<?= $sticky_card_deep_link; ?>" target="_blank" class="ege-sticky-apply-btn">Learn More</a>
  </div>
  <?php endif; ?>

  <?php if ($sticky_params_short !== true): ?>
  <hr />

  <div style="position: relative;">
    <div class="phase">
      <?= $sticky_card->post_content; ?>
    </div>
  </div>

  <button class="ege-sticky-read-more-btn">Read More</button>
  <?php endif; ?>
</div>


<script>
jQuery(document).ready(function($){
  var $readMoreBtn = $('.ege-sticky-read-more-btn').first();
  
  $readMoreBtn.click(function(e){
    e.preventDefault();
    var $phase = $('#ege-sticky-card .phase');
    $phase.toggleClass('expand');
    if ($phase.hasClass('expand')) {
      $readMoreBtn.text('Read Less');
    } else {
      $readMoreBtn.text('Read More');
    }
  });
});
</script>