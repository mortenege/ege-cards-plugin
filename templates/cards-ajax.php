<div class="form-row">
<?php foreach ($posts as $post): ?>
  <?php
    $the_meta = get_post_meta($post->ID);
  ?>

  <div class="col-4">
    <div class="ege-card">
      <div style="margin:0 auto;width:150px;height:100px;background-color:transparent;background-repeat:no-repeat;background-size:contain;background-image: url('<?= get_the_post_thumbnail_url($post); ?>');" >
      </div>
      <h2 class="ege-card-title"><?= $post->post_title; ?></h2>
      <div class="ege-card-header">Callout</div>
      <div class="ege-card-text"><?= $the_meta['callout'][0]; ?></div>
      <div class="ege-card-header">Annual Fee</div>
      <div class="ege-card-text"><?= $the_meta['annual_fee'][0]; ?></div>
      <div class="ege-card-header">Bonus</div>
      <div class="ege-card-text"><?= $the_meta['bonus_value'][0]; ?></div>
    </div>
  </div>
<?php endforeach; ?>
</div>