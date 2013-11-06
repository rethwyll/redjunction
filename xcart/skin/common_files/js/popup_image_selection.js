/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup image selection
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    f816628af5514c4b2ad80d2cc47d0e5923ad3eb7, v4 (xcart_4_6_1), 2013-09-13 15:32:26, popup_image_selection.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Display popup window
 */
function popup_image_selection (type, id, imgid, dialogParams) {

  var opts = {width: 530, height: 430};

  if (undefined !== dialogParams) {
    for (var i in dialogParams) {
      opts[i] = dialogParams[i];
    }
  }

  return popupOpen('image_selection.php?type=' + type + '&id=' + id + '&imgid=' + imgid, '', opts);
}

/**
 * Reset new selected image
 */
function popup_image_selection_reset (type, id, imgid) {
  if (document.getElementById(imgid)) {
    var ts = new Date();
    document.getElementById(imgid).src = xcart_web_dir+"/image.php?type="+type+"&id="+id+"&ts="+ts.getTime();
    if (document.getElementById(imgid+'_text')) {
      document.getElementById(imgid+'_text').style.display = 'none';
      for (var cnt = 1; true; cnt++) {
        if (!document.getElementById(imgid+'_text'+cnt))
          break;
        window.opener.document.getElementById(imgid+'_text'+cnt).style.display = 'none';
      }
    }

    if (document.getElementById('skip_image_'+type))
      document.getElementById('skip_image_'+type).value = 'Y';
    else if (document.getElementById('skip_image_'+type+"_"+id))
      document.getElementById('skip_image_'+type+"_"+id).value = 'Y';

    if (document.getElementById(imgid+'_reset'))
      document.getElementById(imgid+'_reset').style.display = 'none';
  }
}
