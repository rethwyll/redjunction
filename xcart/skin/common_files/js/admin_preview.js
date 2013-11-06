/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Demo preview functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    8656f60383c1ee68345cedf5fd23d5c495b6d050, v3 (xcart_4_6_1), 2013-07-31 12:39:59, admin_preview.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$.event.add(
  window,
  "load",
  function() {
    $('form').unbind('submit').bind(
      'submit',
      function() {
        alert(txt_this_form_is_for_demo_purposes);
        return false;
      }
    );
    $('a:not([href*=#product-tabs])').unbind('click').bind(
      'click',
      function(e) {
        if (this.href && this.href.search(/javascript:/) != -1)
          return false;

        if (!e)
          e = event;

        if (e.stopPropagation)
          e.stopPropagation();
        else
          e.cancelBubble = true;

        alert(txt_this_link_is_for_demo_purposes);
        return false;
      }
    );
  }
);
