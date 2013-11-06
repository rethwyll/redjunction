{*
ef73dfc708dc8113b87c8e77f2cb6fa8f5ff7a3c, v1 (xcart_4_6_0), 2013-05-27 15:54:49, product.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$extra_fields item=v}
  {if $v.active eq 'Y' and $v.field_value}
    <div class="property-name">{$v.field}</div>
    <div class="property-value" colspan="2">{$v.field_value}</div>
    <div class="separator"></div>
  {/if}
{/foreach}
