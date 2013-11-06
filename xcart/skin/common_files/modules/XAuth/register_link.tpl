{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, register_link.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $xauth_register_link_displayed}
  <div class="xauth-register-link">{$lng.lbl_xauth_register_link|substitute:accounts:$xauth_accounts}</div>
{/if}
