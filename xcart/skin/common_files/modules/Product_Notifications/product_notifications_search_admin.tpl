{*
553ad884cebe25b494a85fd5177abb3f8ddf289e, v1 (xcart_4_5_3), 2012-08-15 07:26:38, product_notifications_search_admin.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var searchform_def = [
  ['posted_search_data[type]', ''],
  ['posted_search_data[product]', ''],
  ['posted_search_data[email]', ''],
];
//]]>
</script>

<div>{include file="main/visiblebox_link.tpl" mark="fpn" title=$lng.lbl_prod_notif_adm_search}</div>
{capture name=dialog}
<form name="searchnotificationsform" action="product_notifications.php" method="post">
  <input type="hidden" name="mode" value="search" /> 
  <table cellpadding="1" cellspacing="5" id="boxfpn"{if not $filter_used} style="display: none;"{/if}>

    <tr>
      <td height="10" class="FormButton" width="20%" nowrap="nowrap">{$lng.lbl_prod_notif_adm_type}:</td>
      <td height="10">
        {include file="modules/Product_Notifications/product_notifications_type_selector.tpl" field="posted_search_data[type]" selected=$search_prefilled.type include_all_option='Y'}
      </td>
    </tr>
    
    <tr>
      <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_prod_notif_adm_product}:</td>
      <td height="10">
        <input type="text" name="posted_search_data[product]" size="30" value="{$search_prefilled.product|escape}" />
      </td>
    </tr>

    <tr>
      <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_prod_notif_adm_email}:</td>
      <td height="10">
        <input type="text" name="posted_search_data[email]" size="30" value="{$search_prefilled.email|escape}" />
      </td>
    </tr>

    <tr>
      <td colspan="2">
        <input type="submit" value="{$lng.lbl_prod_notif_adm_search_button|strip_tags:false|escape}" />
        &nbsp;
        <a href="javascript:void(0);" onclick="javascript: reset_form('searchnotificationsform', searchform_def);" class="underline">{$lng.lbl_reset}</a>
      </td>
    </tr>

  </table>
</form>

{/capture}
{include file="dialog.tpl" title="" content=$smarty.capture.dialog extra='width="100%"'}
