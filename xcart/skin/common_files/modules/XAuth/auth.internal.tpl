{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, auth.internal.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" class="xauth-auth xauth-auth-internal">
  <tr>
    <td class="xauth-popup xauth-popup-internal">
      <form action="xauth_internal_openid.php" method="post" name="xaut_openid_form">
        <ul>
          {if $xauth_providers.openid}
            <li class="openid">
              <label for="xauth_openid">{$lng.lbl_xauth_openid}</label>
              <input type="text" id="xauth_openid" name="openid" />
              {include file="customer/buttons/submit.tpl" type="input" additional_button_class="main-button"}
            </li>
          {/if}
        </ul>
      </form>
    </td>
    <td class="xauth-or">{$lng.lbl_or}</td>
    <td class="xauth-form">{include file="customer/main/login_form.tpl"}</td>
  </tr>
</table>
