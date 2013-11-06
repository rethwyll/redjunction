{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, button.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_flc}
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_xauth_open style="button" href="javascript: xauthTogglePopup(this);" additional_button_class="xauth-button"}
{/if}
