{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, register.button.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $xauth_register_button_displayed}
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_xauth_fill_account_from_ext href="javascript: return xauthTogglePopup(this, 'fill');"}
{/if}
