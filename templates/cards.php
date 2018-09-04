<?php
/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$terms = get_terms([
  'taxonomy' => 'card_category',
  'hide_empty' => true,
]);

$tags = get_terms([
  'taxonomy' => 'card_tag',
  'hide_empty' => true,
]);
?>

<div style="width:100%;margin-bottom: 20px;">
  <form class="ege-cards-search-form">
    <div class="ege-cards-form-group">
      <label class="ege-cards-search-label">Select card category:</label>
      <select class="ege-cards-search-select" id="ege-cards-category">
        <option value="" selected>All Categories</option>
        <?php foreach ($terms as $term): ?>
          <?php if ($term->parent === 0): ?>
            <option value="<?= $term->slug; ?>"><?= $term->name ?></option>
            <?php foreach ($terms as $child): ?>
              <?php if ($child->parent === $term->term_id): ?>
                <option value="<?= $child->slug; ?>">&nbsp;&nbsp;<?= $child->name ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="ege-cards-form-group">
      <label class="ege-cards-search-label">Select Annual Fee:</label>
      <select class="ege-cards-search-select" id="ege-cards-tag">
        <option value="" selected>All Fees</option>
        <?php foreach ($tags as $tag): ?>
          <option value="<?= $tag->slug; ?>"><?= $tag->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    
    <input type="text" name="search" id="ege-cards-search" placeholder="Search card name" class="ege-cards-search-input"/>
    
  </form>
  
  <hr class="hide-on-mobile" style="background-color: #212529;"/>

</div>


<div id="ege-cards-list">Loading...</div>

<script>
/**
 * Standard debounce function
 */
function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

jQuery(document).ready(function($){
  var $inputSearch = $('#ege-cards-search');
  var $selectCategory = $('#ege-cards-category');
  var $selectTag = $('#ege-cards-tag');
  var $cardContainer = $('#ege-cards-list');

  $inputSearch.on('input', debounce(ege_search_cards, 250) );
  $selectTag.change(ege_search_cards);
  $selectCategory.change(ege_search_cards);

  function ege_search_cards () {
    var data = {
      action: 'ege_cards_search_cards',
      search: $inputSearch.val(),
      tag: $selectTag.val(),
      category: $selectCategory.val()
    }
    var url = "<?= admin_url('admin-ajax.php'); ?>";

    $cardContainer.empty();
    $cardContainer.html('Loading...');
    $.get(url, data, function (response, status){
      $cardContainer.html(response);
    });
  }

  ege_search_cards();
});
</script>