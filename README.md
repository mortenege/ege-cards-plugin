# README

Custom Wordpress plugin for [SimpleFlying.com](https://simpleflying.com)

## Shortcodes

 * `[ege_cards]` shows all the cards in a grid with search functionality
 * `[ege_cards_sticky_card]` shows the selected **sticky** card.
    * `short` (boolean) show a short version. Values "true" or "false" 
    * example `[ege_cards_sticky_card short="true|false"]`
 * `[ege_cards_disclaimer]` show disclaimer
    * `type` (string) value "bottom" or "top"
    * example `[ege_cards_disclaimer type="bottom"]`
 * `[ege_cards_link id="<ID>"]` where ID is the Id of a Travel Card.


## Wordpress Admin Interface

* Custom `Post type`: *travelcard*
    * Custom Meta Box to add meta
    * Custom Taxonomies: *Category* (hierarchical) and *Tag* (flat)
    * Upload Travel Card image via *Featured Image*
    * Add to any page with shortcode `[ege_cards]` and `[ege_cards_sticky_card]`
    * Custom Search Widget to filter credit cards in backend
    * Custom User Priviliges attached
* Easy Insert of Travelcard links with "Add Travel Card Link" in *edit* page.
    * This inserts a shortcode [ege_cards_link id="(id)" caption="(meta_field_name)"]
    * All occurrences of the links are counted to display how many pages on which they occurr ("Page Displays")
    * All clicks on the links are also counted ("Click Count"). **NOTE** this is only the case if "*Enable Travelcard click counting*" is set under *general* settings.

## Wordpress Front End
 
 * CSS3 grid
 * jQuery AJAX calls to backend for search function (with debounce)
