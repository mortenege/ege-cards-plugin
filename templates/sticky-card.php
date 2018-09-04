<?php 
$sticky_card_meta = get_post_meta($sticky_card->ID);
$sticky_card_title = $sticky_card_meta['long_title'];
$sticky_card_title = is_array($sticky_card_title) ? $sticky_card_title[0] : $sticky_card_title;
$sticky_card_title = $sticky_card_title ? $sticky_card_title : $sticky_card->post_title;

$sticky_card_deep_link = $sticky_card_meta['deep_link'];
$sticky_card_deep_link = is_array($sticky_card_deep_link) ? $sticky_card_deep_link[0] : $sticky_card_deep_link;
$sticky_card_deep_link = $sticky_card_deep_link ? $sticky_card_deep_link : '#';
?>
<div class="ege-sticky-card">
  
  <div class="ege-sticky-title"><?= $sticky_card_title; ?></div>
  
  <div class="ege-sticky-table">
    <div class="ege-sticky-excerpt"><?= $sticky_card->post_excerpt; ?></div>
    <div class="ege-sticky-travelcard-container">
      <div class="ege-sticky-travelcard" style="background-image: url('<?= get_the_post_thumbnail_url($sticky_card); ?>');"></div>
    </div>
  </div>
  
  <div class="mb-2">
    <a href="<?= $sticky_card_deep_link; ?>" class="ege-sticky-apply-btn">Apply now</a>
  </div>

  <hr />
  <div style="position: relative;">
    <div class="phase">
      <?= $sticky_card->post_content; ?>
    </div>
  </div>

  <button class="ege-sticky-read-more-btn">Read More</button>
</div>

<style>
.ege-sticky-card {
  width: 100%;
  background-color: #FFF;
  font-family: "Welsheim", Arial, Helvetica, sans serif;
  color: rgb(32, 32, 38);
  border: 1px solid rgb(229, 229, 229);
  border-top: 5px solid rgb(32, 32, 38);
  padding: 20px;
}
.ege-sticky-card .ege-sticky-title {
  font-size: 30px;
  font-weight: 700;
  font-style: normal;
  line-height: 36px;
  margin-bottom: 20px;
}

.ege-sticky-card .ege-sticky-table {
  display: block;
  margin-bottom: 20px;
}
.ege-sticky-card .ege-sticky-excerpt {
  display: block;
  width: 100%;
  font-size: 18px;
  font-style: normal;
  font-weight: 200;
  line-height: 33px;
  margin-bottom: 20px;
}
.ege-sticky-card .ege-sticky-travelcard-container {
  display: block;
  width: 100%;
}
.ege-sticky-card .ege-sticky-travelcard {
  margin: 0 auto;
  width: 150px;
  height: 100px;
  background-color: transparent;
  background-repeat: no-repeat;
  background-size: contain;
}

@media only screen and (min-width: 768px) {
  .ege-sticky-card .ege-sticky-table {
    display: table;
  }
  .ege-sticky-card .ege-sticky-excerpt {
    display: table-cell;
    width: auto;
    vertical-align: top;
  }
  .ege-sticky-card .ege-sticky-travelcard-container {
    display: table-cell;
    vertical-align: middle;
    width: 300px;
  }
}

.ege-sticky-card .ege-sticky-apply-btn {
  background-color: rgb(42, 42, 68);
  font-family: "Montserrat", Arial, Helvetics, sans serif;
  font-weight: bold;
  text-align: center;
  font-size: 18px;
  line-height: 22px;
  color: #FFF;
  padding: 10px 20px;
  margin: 0 auto;
  display: block;
  width: 100%;
  max-width: 300px;
  box-shadow: none;
  border-radius: 0;
  transition: border-radius 0.4s;
}

.ege-sticky-card .phase {
  height: 200px;
  overflow-y: hidden;
}
.ege-sticky-card .phase.expand {
  height: auto;
  overflow-y: auto;
}
.ege-sticky-card .phase:not(.expand):before {
  content:'';
  width:100%;
  height:100%;    
  position:absolute;
  left:0;
  top:0;
  background:linear-gradient(transparent 100px, white);
}
.ege-sticky-card .ege-sticky-read-more-btn,
.ege-sticky-card .ege-sticky-read-more-btn:hover,
.ege-sticky-card .ege-sticky-read-more-btn:active,
.ege-sticky-card .ege-sticky-read-more-btn:visited,
.ege-sticky-card .ege-sticky-read-more-btn:focus {
  background-color: #FFF;
  color: rgb(32, 32, 38);
  width: 100%;
  box-shadow: none;
}
.ege-sticky-card .ege-sticky-read-more-btn:hover {
  text-decoration: underline;
}
</style>

<script>
jQuery(document).ready(function($){
  var $readMoreBtn = $('.ege-sticky-read-more-btn').first();
  
  $readMoreBtn.click(function(e){
    e.preventDefault();
    var $phase = $('.ege-sticky-card .phase').first();
    console.log('---->', $phase);
    $phase.toggleClass('expand');
  });
});
</script>