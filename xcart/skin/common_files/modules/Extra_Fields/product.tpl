{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, product.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$extra_fields item=v}
  {if $v.active eq "Y" and $v.field_value}
    <tr>
      <td class="property-name">{$v.field}</td>
      <td class="property-value" colspan="2">{$v.field_value}</td>
    </tr>
  {/if}
{/foreach}
