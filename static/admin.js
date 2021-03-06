jQuery(document).ready(function($){
  var data = window.travelcardData, // load global card data
      searchVal = "", // remember search values
      filteredData = {},  // show these results
      resultsContainer = $('#ege-cards-link-inserter-results'),
      related_id = 0

  /**
   * Insert SHORTCODE into editor when specific caption is clicked
   */
  resultsContainer.on('click', '.ege-cards-link-inserter-btn', function(e){
    var id = $(this).attr('data-travelcard-id');
    var caption = $(this).attr('data-travelcard-caption');
    wp.media.editor.insert('[ege_cards_link id="' + id + '" caption="' + caption + '"]');
    tb_remove();
  });

  $('#ege-cards-related-inserter').change(function(event){
    let id = $(this).val();
    related_id = id;
  });

  $('#ege-cards-related-btn').click(function(event){
    let id = $('#ege-cards-related-inserter').val();
    wp.media.editor.insert('[ege_cards_related id="' + related_id + '"]');
    tb_remove();
  });

  /**
   * Search, filter and manipulate DOM while searching
   */
  $('#ege-cards-link-inserter-input').keyup(function(e){
    var val = $(this).val();
    if (val != searchVal) {
      searchVal = val;
      resultsContainer.empty();
      if (val == "") return;

      // set filtered data
      filteredData = data.filter((obj) => {
        if (obj.post_title && obj.post_title.toLowerCase().indexOf(val.toLowerCase()) != -1) return true;
        if (obj.m1 && obj.m1.toLowerCase().indexOf(val.toLowerCase()) != -1) return true;
        if (obj.m2 && obj.m2.toLowerCase().indexOf(val.toLowerCase()) != -1) return true;
        if (obj.m3 && obj.m3.toLowerCase().indexOf(val.toLowerCase()) != -1) return true;
        if (obj.m4 && obj.m4.toLowerCase().indexOf(val.toLowerCase()) != -1) return true;
        if (obj.m5 && obj.m5.toLowerCase().indexOf(val.toLowerCase()) != -1) return true;
        return false;
      });

      // Manipulate DOM
      for (let i in filteredData){
        let obj = filteredData[i];
        let div = $('<div class="ege-cards-link-inserter-box">');
        div.append($('<div class="ege-cards-link-inserter-title">').text(obj.post_title));

        for (let i=1; i<=5; i++) {
          if (obj['m'+i]) {
            let btn = $('<btn class="ege-cards-link-inserter-btn">')
              .attr('data-travelcard-id', obj.ID)
              .attr('data-travelcard-caption', 'official_name_'+i)
              .text(obj['m'+i]);
            div.append(btn);
          }  
        }

        let btn = $('<btn class="ege-cards-link-inserter-btn">')
          .attr('data-travelcard-id', obj.ID)
          .attr('data-travelcard-caption', 'EDIT ME! EDIT ME!')
          .text('Custom');
        div.append(btn);

        /*
        if (!obj.m1 && !obj.m2 && !obj.m3 && !obj.m4 && !obj.m5) {
          div.append($('<small>').text('No official titles found. Please Edit card.'));
        }
        */
        resultsContainer.append(div);
      };
    }
  });
});