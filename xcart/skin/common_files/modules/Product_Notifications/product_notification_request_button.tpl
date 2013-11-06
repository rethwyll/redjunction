{*
b7e8f9878e58a3125c64465dbf28f4d86899c127, v2 (xcart_4_5_4), 2012-10-19 05:12:18, product_notification_request_button.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var='tip_lbl_name' value="lbl_prod_notif_button_tip_`$type`"}
<a id="prod_notif_request_button_{$productid}_{$type}" class="prod-notif-request-button prod-notif-request-button-{$type}" href="javascript:void(0);" title="{$lng.$tip_lbl_name}"><img src="{$ImagesDir}/spacer.gif" alt="" /></a>
