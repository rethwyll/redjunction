{*
8ec8b858917c921a726e2197071ce74d020e5ead, v1 (xcart_4_6_0), 2013-04-02 16:43:59, head.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="line1">
  <div class="logo">
    <a href="{$catalogs.customer}/home.php"><img id="main_logo" src="{$ImagesDir}/spacer.gif" alt="Logo Placeholder" /></a>
  </div>
  <div class="header-links">
		<div class="wrapper">
			{include file="customer/header_links.tpl"}
		</div>
  </div>
  {include file="customer/tabs.tpl"}

  {include file="customer/phones.tpl"}

</div>

<div class="line2">
  {if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}

    {include file="customer/search.tpl"}

    {include file="customer/language_selector.tpl"}

  {/if}
</div>

{include file="customer/noscript.tpl"}
