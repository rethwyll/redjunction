{*
fafb6e680e507242d4d3dc09c88872c868cb70b4, v1 (xcart_4_5_0), 2012-04-06 10:32:27, cart_totals.tpl, ferz
vim: set ts=2 sw=2 sts=2 et:

TaxCloud module: Add/update/remove exemption certificate to cart
*}

{if $active_modules.TaxCloud && $login}

{if $cart.taxcloud_certificateID}

<div class="taxcloud-cert dcoupons-clear">
  <span id="xmptlink" class="navlink" title="{$lng.taxcloud_lbl_exemption_cert}">{$cart.taxcloud_certificateID}</span>
  <a href="cart.php?mode=checkout&amp;action=taxcloud_clear" class="unset-cert-link" title="{$lng.taxcloud_lbl_unset_certificate|escape}"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.taxcloud_lbl_unset_certificate|escape}" /></a>
</div>

{else}

<div class="taxcloud-cert">
  <span id="xmptlink" class="navlink">{$lng.taxcloud_lbl_are_you_exempt}</span>
</div>

{/if}

<input type="hidden" id="taxcloud_exemption_certificate">

<hr />

{/if}
