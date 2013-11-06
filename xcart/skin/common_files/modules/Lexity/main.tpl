{*
92c570389c548fed231fa5880254b1dbcad5b419, v3 (xcart_4_5_2), 2012-07-11 10:07:49, main.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="page_title.tpl" title=$lng.lbl_lexity_title}

{capture name=dialog}

<iframe src="http://lexity.com/embed?p={$lexity_partner_code}&h={$lexity_render_hash}&id={$lexity_merchant_id}&e={$lexity_email}&u={$lexity_store_url}" height="800" width="1000" style="border: 0;"></iframe>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_lexity_iframe_title content=$smarty.capture.dialog extra='width="100%"'}
