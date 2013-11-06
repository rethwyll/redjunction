/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Pop up users
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    e6c9ab1e8b9c6c9cd78718d74740f680184ba36c, v4 (xcart_4_5_3), 2012-08-03 07:47:34, popup_users_open.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function open_popup_users(form, format, force_submit, single_selection) {
  single_selection = (typeof single_selection == 'undefined') ? false : single_selection;
  return window.open ("popup_users.php?form="+form+"&format="+escape(format)+'&force_submit='+(force_submit ? "Y" : "")+'&single_selection='+(single_selection ? "Y" : ""), "selectusers", "width=700,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
