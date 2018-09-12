<?php add_thickbox(); ?>
<div id="ege-cards-custom-link-manager" style="display:none;">
  <p>Insert an affiliate link via a travel card. <small>Clicking a title will insert the shortcode in to the post.</small></p>
  <input type="text" style="margin-bottom:5px;width:200px;" id="ege-cards-link-inserter-input" placeholder="Search Card name" />
  <div id="ege-cards-link-inserter-results"></div>
  <style>
    .ege-cards-link-inserter-box {
      border: 1px solid #ddd;
      padding: 10px;
      margin-bottom: 5px;
    }
    .ege-cards-link-inserter-title {
      font-size: 16px;
      font-weight: 800;
      line-height: 20px;
      margin-bottom: 5px;
    }
    .ege-cards-link-inserter-btn {
      display: inline-block;
      background-color: #fcfcfc;
      border: 1px solid #ddd;
      font-size: 13px;
      line-height: 26px;
      padding: 2px 5px;
      margin-right: 5px;
      cursor: pointer;
      color: #555;
      border-radius: 3px;
      box-shadow: 0 1px 0 #ccc;
      text-decoration: none;
    }
    .ege-cards-link-inserter-btn:hover {
      border: 1px solid #999;
      background-color: #fafafa;
      color: #23282d;
    }
  </style>
</div>
<a href="#TB_inline?width=600&height=550&inlineId=ege-cards-custom-link-manager" class="button thickbox">Add Travel Card link</a>
<script>
window.travelcardData = JSON.parse('<?= json_encode($results); ?>');
</script>