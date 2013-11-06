/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Reviews widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v4 (xcart_4_6_0), 2013-05-22 14:21:42, func.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$(document).ready(function(){

  $('#email')
    .blur(function(){
      $('#email_note').hide();
    })
    .focus(function(){
      showNote('email_note', this);
    }
  );

  /*
   * Useful block processing
   */
  links = $('.useful-yes, .useful-no');
  links.click(function(){
 
      voteForReview(this);
      return false;

  });

  function voteForReview(obj) {

      $obj = $(obj);
    
      var $useful_block = $obj.parent('div');

      var $waiting_block = $obj.parent('div').children('.wait');
      $waiting_block.show();

      var href = $obj.attr('href');

      $.get(
        href,
        function(data) {

          $waiting_block.hide();

          if (!data) {
            return false;
          }

          $useful_block.html(data);
          newlinks = $('.useful-yes, .useful-no');
          newlinks.click(function(){
            voteForReview(this);
            return false;
          });

        }     
      );

      return false;
  };

  var static_popup_container = 'div.acr-static-popup-container';
  var popup_internal_container_class = 'acr-popup-internal-container';
  var dropdown_button_selector = '.acr-general-product-rating .dropdown-button';

  /*
   * Show/hide separate ratings for certain product in the popup div
   * state == 1: popup container is displayed or ready to be displayed
   * state == 2: popup container is in progress to be displayed
   */
  $(dropdown_button_selector).click(function(){
      if (this._state == 2) return false;

      if (this._visible == true) {
        hideDetailedRatings(this);
      } else {
        displayDetailedRatings(this);
      }

      return false;
  });

  $('body').click(function(){
    hideAllDetailedRatings();
  });

  /*
   * AJAX-based loading of the separate ratings to the popup div
   */
  function displayDetailedRatings(obj) {

      if (obj._state == 2) {
        return false;
      }

      var $obj = $(obj);

      if (!obj._href) {
        var href = $obj.children('a').filter(':first').attr('href');
        obj._href = href;
      }

      if (!obj._href)
        return false;

      obj._visible = true;
      obj._state = 2;
      changeButtonState(obj, 'up'); 

      var dynamic_popup_container = document.createElement('DIV');
      $(dynamic_popup_container).addClass(popup_internal_container_class);
        
      var content = document.createElement('DIV');
      $(content).addClass('progress');
      $(dynamic_popup_container).append(content);

      var $static_container = $obj.parents('div').find(static_popup_container).first();
      $static_container.append(dynamic_popup_container);

      // Load data about separate ratings to the popup box
      $.get(
        obj._href,
        function(data) {

          obj._state = 1;

          $(dynamic_popup_container).children('.progress').remove();

          if (!data) {
            return false;
          }

          $(dynamic_popup_container).append(data);
          

          $(dynamic_popup_container).click(function(e){
            e.stopPropagation();
          });
        }
      );
  }

  /* 
   * Hide popup div with separate ratings
   */
  function hideDetailedRatings(obj) {
      changeButtonState(obj, 'down');

      obj._visible = false;
      obj._state = 1;

      $remove_block = $(obj).parents('div').find(static_popup_container).children('.' + popup_internal_container_class);
      $remove_block.fadeOut(
        100,
        function() {
          $remove_block.remove();
        }
      );
  }

  function hideAllDetailedRatings(target) {

      $(dropdown_button_selector).each(function(){
        if (this._visible) {
          hideDetailedRatings(this);
        }
      });

  }

  function changeButtonState(obj, state) {
      $img = $(obj).children('a').children('img');

      if (state == 'up' || state == 'down') {
        $img.attr('src', images_dir + '/acr_reviews_dropout_' + state + '.png');
      }
  }

});
