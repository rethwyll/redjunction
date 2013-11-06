/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * PayPal Access popup script
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage PayPal Access
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    3d011a81f50829becf5ff134569a5785ba36c8da, v8 (xcart_4_5_3), 2012-09-13 08:26:27, ppa.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var ppa_clicked = false;

function open_popup_ppa(url) {

	if (!ppa_clicked) {
		ppa_clicked = true;
		var ppa_popup = window.open (url, 'PPA_identity_window_', 'location=yes,status=no,scrollbars=no,menubar=no,toolbar=no,width=380,height=592');
		jQuery(ppa_popup).ready(function() {
			ppa_clicked = false;
		});

		jQuery(ppa_popup).unload(function() {
			ppa_clicked = false;
		});
	}

	return true;
}
