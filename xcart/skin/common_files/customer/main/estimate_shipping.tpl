{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, estimate_shipping.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_zipcode_js.tpl"}
{include file="change_states_js.tpl"}

<form action="popup_estimate_shipping.php" method="post" name="userinfoform">
<input type="hidden" name="mode" value="change_address" />

  <table cellspacing="1" class="change-userinfo" cellpadding="3">

    {include file="customer/main/address_fields.tpl" address=$address name_prefix="change_userinfo" id_prefix='change_userinfo_' default_fields=$shipping_estimate_fields}

    <tr>
      <td align="center" colspan="3">
        {include file="customer/buttons/submit.tpl" type="input"}
      </td>
    </tr>

  </table>

</form>
