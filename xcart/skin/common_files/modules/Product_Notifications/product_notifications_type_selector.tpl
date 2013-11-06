{*
553ad884cebe25b494a85fd5177abb3f8ddf289e, v1 (xcart_4_5_3), 2012-08-15 07:26:38, product_notifications_type_selector.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $field eq ''}
  {assign var=field value="type"}
{/if}

<select name="{$field}">
  {if $include_all_option eq 'Y'}
    <option value=""{if not $selected or $selected eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
  {/if}
  {foreach from=$config.Product_Notifications.notification_types item=type}
    {assign var="notification_type_name" value="lbl_prod_notif_adm_type_name_`$type`"}
    <option value="{$type}"{if $selected eq $type} selected="selected"{/if}>{$lng.$notification_type_name}</option>
  {/foreach}
</select>
