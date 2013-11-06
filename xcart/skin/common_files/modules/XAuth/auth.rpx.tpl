{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, auth.rpx.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.XAuth.xauth_rpx_display_mode eq 'v'}
  {include file="modules/XAuth/auth.rpx.vertical.tpl"}
{else}
  {include file="modules/XAuth/auth.rpx.horizontal.tpl"}
{/if}
