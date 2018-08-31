<?php
  $terms = get_terms([
    'taxonomy' => 'card_category',
    'hide_empty' => true,
  ]);

  $tags = get_terms([
    'taxonomy' => 'card_tag',
    'hide_empty' => true,
  ]);
?>

<div class="card w-100 mb-4">
  <div class="card-body">
    <div class="form-row">
      <div class="col-12">
        <div class="form-group">
          <input type="text" name="search" id="ege-cards-search" placeholder="Search Card Name" class="form-control"/>
        </div>
      </div>
      <div class="col-12 col-sm-6">
        <div class="form-group">
          <label>Filter by Category</label>
          <select class="form-control" id="ege-cards-category">
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
      </div>
      <div class="col-12 col-sm-6">
        <div class="form-group">
          <label>Filter by Annual Fee</label>
          <select class="form-control" id="ege-cards-tag">
            <option value="" selected>All Tags</option>
            <?php foreach ($tags as $tag): ?>
              <option value="<?= $tag->slug; ?>"><?= $tag->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>
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
  font-weight: 600;
  font-size: 18px;
  color: white;
  padding: 5px;
  text-align: center;
  margin-bottom: 20px;
  min-height: 100px;
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
  text-align: left;
  margin: 0 20px;
  font-weight: 600;
}
.ege-card-header {
  font-weight: 700;
  font-size: 14px;
  line-height: 14px;
  margin: 0 20px;
}
.ege-card-text {
  line-height: 12px;
  font-size: 12px;
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
  color: white;
  font-weight: 600;
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
  color: #000;
  text-decoration: none;
}
.ege-card-terms-link:hover {
  text-decoration: underline;
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