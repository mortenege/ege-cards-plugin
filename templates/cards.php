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
  text-align: left;
  margin: 2px;
  padding: 10px;
  padding-top: 20px;
  border-radius: 10px;
  box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.3);
}

.ege-card-title {
  text-align: center;
}

.ege-card-header {
  font-weight: 800;
  font-size: 16px;
}

.ege-card-text {
  font-size: 12px;
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
    console.log('-------->', url, data);

    $cardContainer.empty();
    $cardContainer.html('Loading...');
    $.get(url, data, function (response, status){
      $cardContainer.html(response);
    });
  }

  ege_search_cards();
});
</script>