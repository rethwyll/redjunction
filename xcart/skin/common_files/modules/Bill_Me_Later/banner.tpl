{*
c4876f0dc3c1be77d432d7277a473c36b8ae058c, v1 (xcart_4_6_1), 2013-09-07 11:40:24, banner.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $bml_page eq 'home'}
  {assign var='bml_location' value=$config.Bill_Me_Later.bml_banner_on_home}
{elseif $bml_page eq 'category'}
  {assign var='bml_location' value=$config.Bill_Me_Later.bml_banner_on_category}
{elseif $bml_page eq 'product'}
  {assign var='bml_location' value=$config.Bill_Me_Later.bml_banner_on_product}
{elseif $bml_page eq 'cart'}
  {assign var='bml_location' value=$config.Bill_Me_Later.bml_banner_on_cart}
{/if}
{if $bml_location ne ''}
{include file="modules/Bill_Me_Later/placement_types.tpl"}
<div class="paypal-bml-banner {$bml_page} {$bml_location}">
<script type="text/javascript" data-pp-pubid="{$config.paypal_bml_publisherid}" data-pp-placementtype="{$smarty.capture.bml_placementtype}">
//<![CDATA[
{literal}
(function (d, t) {
"use strict";
var s = d.getElementsByTagName(t)[0], n = d.createElement(t);
n.src = "//paypal.adtag.where.com/merchant.js";
s.parentNode.insertBefore(n, s);
}(document, "script"));
{/literal}
//]]>
</script>
</div>
{/if}
