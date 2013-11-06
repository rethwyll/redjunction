{*
1a815d7241d4b322b85c9034e1c3cc69530b3c99, v2 (xcart_4_4_0), 2010-07-22 12:02:31, head.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="line1">
  <div class="logo">
    <a href="{$catalogs.customer}/home.php"><img src="{$ImagesDir}/xlogo.gif" alt="" /></a>
  </div>

  {include file="customer/tabs.tpl"}

  {include file="customer/phones.tpl"}

  {if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}

    {include file="customer/language_selector.tpl"}

  {/if}
</div>

{if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}
  <div class="line2">
    {include file="customer/search.tpl"}

    <div class="auth">



        {include file="customer/authbox.tpl"}

    </div>

    {if ($main ne "catalog" or $current_category.category ne "") && $printable ne 'Y'}
      <div class="subauth-line">
        {include file="customer/bread_crumbs.tpl"}
      </div>
    {/if}
  </div>
{/if}

{include file="customer/noscript.tpl"}
