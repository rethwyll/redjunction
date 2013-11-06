{*
e66af9def9eb9914ed35b2e42b34821337ba81e2, v2 (xcart_4_5_4), 2012-10-24 13:39:58, jquery_loadmask_js.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{load_defer file="lib/jquery.loadmask.js" type="js"}
{capture name=loadmask}
var lbl_loading = '{$lng.lbl_loading|wm_remove|escape:"javascript"}';
{/capture}
{load_defer file="loadmask" direct_info=$smarty.capture.loadmask type="js"}
