{*
5bae77b73876313d8cb8257067ff0e846700198c, v2 (xcart_4_5_3), 2012-09-26 08:24:16, rpx_ss_cart_item.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.XAuth.xauth_enable_ss_cart eq 'Y'}
<div class="xauth-rpx-ss-cart-item-button">
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_xauth_rpx_share href="javascript: return !xauthOpenCartItemShare(this);" additional_button_class="light-button"}
  <a href="javascript:void(0);" onclick="return !xauthOpenCartItemShare(this);" class="xauth-ss-link">
    {include file="modules/XAuth/rpx_cc_icons.tpl"}
    {$lng.lbl_xauth_rpx_share_and_more}
  </a>
</div>
{/if}
