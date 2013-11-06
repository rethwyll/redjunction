{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v5 (xcart_4_6_1), 2013-08-26 17:55:46, cc_xpc.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>X-Payments payment methods</h1>

<br />
<br />

{capture name=dialog}

<img src="{$ImagesDir}/xpc_logo.png" width="130" height="55" alt="X-Payments logo" />

<br />
<br />
<br />

{$lng.txt_xpc_pm_config_note}

<br />
<br />

<form action="cc_processing.php?mode=update_xpc" method="post">

<table cellpadding="5" cellspacing="1" border="0">

  <tr class="TableHead">
    <td>{$lng.lbl_payment_method}</td>
    <td>{$lng.lbl_xpc_pm_id}</td>
    <td>{$lng.lbl_xpc_sale}</td>
    <td>{$lng.lbl_xpc_auth}</td>
    <td>{$lng.lbl_xpc_capture}</td>
    <td>{$lng.lbl_xpc_void}</td>
    <td>{$lng.lbl_xpc_refund}</td>
    <td>{$lng.lbl_xpc_use_recharges}</td>
  </tr>

  {foreach from=$cc_processors item=pm}
  <tr{cycle values=', class="TableSubHead"'}>
    <td>{$pm.module_name}</td>
    <td>{$pm.param01}</td>
    <td>{if $pm.param06 eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>{if $pm.has_preauth eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>{if $pm.param02 eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>{if $pm.is_refund eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>&nbsp;</td>
    <td>
      <input type="checkbox" name="use_recharges[]" value="{$pm.paymentid}" {if $pm.use_recharges eq "Y"}checked="checked"{/if}/>
    </td>
  </tr>
  {/foreach}

</table>

<input type="submit" value="{$lng.lbl_update}">

</form>

<br />
<br />

{$lng.txt_xpc_pm_config_note_2}

<br />
<br />

<a href="configuration.php?option=XPayments_Connector">{$lng.lbl_xpc_xpayments_connector_settings}</a>

<br />
<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
