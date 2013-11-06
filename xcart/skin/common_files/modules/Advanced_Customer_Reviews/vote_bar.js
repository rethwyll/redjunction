/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Voting widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, vote_bar.js, tito
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$(document).ready(
  function() {
    $('.acr-rating-box .acr-allow-add-rate a').hover(
      function() {

        if (!this._previous) {
          this._previous = $(this).prevAll();
        }

        $(this).addClass('over');
        this._previous.addClass('over');
      },
      function() {

        if (!this._previous) {
          this._previous = $(this).prevAll();
        }

        this._previous.removeClass('over');
        $(this).removeClass('over');
      }
    )
    .click(
      function() {

        if (!this._previous) {
          this._previous = $(this).prevAll();
        }

        if (!this._all) {
          this._all = $(this).parent().children('a');
        }

        this._all.removeClass('full');
        $(this).addClass('full');
        this._previous.addClass('full');

        if (!this._rating) {
          var _rating = parseInt($(this).attr('id').match(/\d+/));
          this._rating = _rating * stars_cost;
        }

        $("#rating").val(this._rating);
    
      }
    );

    return true;
  }
);
