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

<div class="w-100 mb-4">
  <form class="ege-cards-search-form">
    <div class="ege-cards-form-group">
      <label class="ege-cards-search-label">Select card category:</label>
      <select class="form-control ege-cards-search-select" id="ege-cards-category">
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
      <select class="form-control ege-cards-search-select" id="ege-cards-tag">
        <option value="" selected>All Fees</option>
        <?php foreach ($tags as $tag): ?>
          <option value="<?= $tag->slug; ?>"><?= $tag->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    
    <input type="text" name="search" id="ege-cards-search" placeholder="Search card name" class="form-control ege-cards-search-input"/>
    
  </form>
  <hr style="background-color: #212529;"/>
</div>


<div id="ege-cards-list">Loading...</div>

<style>
.ege-card {
  position: relative;
  text-align: left;
  background-color: rgb(241, 241, 243);
  height: 100%;
  width: 100%;
}
.ege-card-callout {
  background-color: rgb(77, 124, 228);
  font-family: "Montserrat", arial, sans serif;
  font-weight: bold;
  font-size: 18px;
  line-height: 22px;
  color: #FFF;
  text-align: center;
  margin-bottom: 20px;
  padding: 0 5px;
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.ege-card-image {
  margin: 0 auto;
  width: 150px;
  height: 100px;
  background-color: transparent;
  background-repeat: no-repeat;
  background-size: contain;
}
.ege-card-title {
  line-height: 29px;
  font-size: 24px;
  font-family: "Montserrat", arial, sans serif;
  font-weight: bold;
  color: #212529;
  text-align: left;
  margin: 0 20px;
}
.ege-card-header {
  font-family: "Opensans", Arial, Helvetics, sans serif;
  font-weight: bold;
  font-size: 14px;
  line-height: 14px;
  color: #212529;
  margin: 0 20px;
}
.ege-card-text {
  font-family: "Opensans", Arial, Helvetics, sans serif;
  font-weight: normal;
  line-height: 14px;
  font-size: 14px;
  margin: 0 20px;
}
.ege-card-text + .ege-card-header {
  margin-top: 10px;
}
.ege-card-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  text-align: center;
  height: 80px;
  padding: 0 20px;
}
.ege-card-apply-btn,
.ege-card-apply-btn:hover,
.ege-card-apply-btn:active,
.ege-card-apply-btn:focus,
.ege-card-apply-btn:visited {
  background-color: rgb(42, 42, 68);
  font-family: "Montserrat", Arial, Helvetics, sans serif;
  font-weight: bold;
  font-size: 18px;
  line-height: 22px;
  color: #FFF;
  padding: 10px 20px;
  margin: 0 auto;
  display: block;
  width: 100%;
  box-shadow: none;
  border-radius: 0;
  transition: border-radius 0.4s;
}
.ege-card-apply-btn:hover {
  text-decoration: underline;
  border-radius: 5px;
}
.ege-card-terms-link,
.ege-card-terms-link:active,
.ege-card-terms-link:focus,
.ege-card-terms-link:visited,
.ege-card-terms-link:hover {
  font-size: 10px;
  line-height: 10px;
  color: #212529;
  font-family: "Opensans", Arial, Helvetics, sans serif;
  font-weight: normal;
  text-decoration: none;
}
.ege-card-terms-link:hover {
  text-decoration: underline;
}

.ege-cards-search-form {
  font-size: 12px;
  display: block;
}
.ege-cards-form-group {
  display: block;
}
.ege-cards-search-label {
  font-weight: 800;
  display: block;
}
.ege-cards-search-select {
  font-size: 12px;
}
.ege-cards-search-input {
  font-size: 12px;
  line-height: 1.5rem;
  display: block;
  width: 100%;
  margin-top: 10px;
}
@media only screen and (min-width: 768px) {
  .ege-cards-search-form {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
  }
  .ege-cards-form-group {
    display: inline;
  }
  .ege-cards-search-label {
    margin-right:5px;
    display: inline;
  }
  .ege-cards-search-select {
    width: auto;
    display: inline;
  }
  .ege-cards-search-input {
    width: 100%;
  }
}
@media only screen and (min-width: 992px) {
  .ege-cards-search-form {
    display: flex;
    justify-content: space-between;
    flex-wrap: nowrap;
  }
  .ege-cards-search-label {}
  .ege-cards-search-input {
    min-width: 300px;
    margin-top: 0;
    width: auto;
  }
  .ege-cards-search-select {}
}
</style>

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