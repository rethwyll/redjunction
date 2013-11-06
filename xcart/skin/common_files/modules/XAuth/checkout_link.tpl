{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, checkout_link.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $xauth_checkout_link_show}
  {$lng.lbl_xauth_opc_sign_in|substitute:sign_in_link:$smarty.capture.loginbn:accounts:$xauth_accounts}
{/if}
