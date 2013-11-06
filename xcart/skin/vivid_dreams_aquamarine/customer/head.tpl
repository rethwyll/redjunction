{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, head.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
{if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}
  <div class="head-bg">
    <div class="head-bg2">

      {include file="customer/language_selector.tpl"}
      {include file="customer/phones.tpl"}

      <div class="logo"><a href="{$catalogs.customer}/home.php"><img src="{$AltImagesDir}/vivid_dreams/logo.gif" alt="" /></a></div>
      <div class="logo_err">
      <a href="home.php"><img src="{$AltImagesDir}/vivid_dreams/logo_check.gif" alt="" /></a>
      </div>
    </div>

    <div class="cart-container">
      <div class="cart-block">
        {include file="customer/menu_cart.tpl"}



        {include file="customer/authbox.tpl"}

      </div>
    </div>

    <div class="line2">
      {include file="customer/search.tpl"}
      {include file="customer/tabs.tpl"}
    </div>

  </div>

{else}

  {include file="modules/`$checkout_module`/head.tpl"}

{/if}

{include file="customer/noscript.tpl"}
