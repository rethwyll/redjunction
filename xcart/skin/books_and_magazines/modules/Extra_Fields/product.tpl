{*
59b18741a7e0c882b9e5cd007ec33ae63ba56ab6, v1 (xcart_4_5_0), 2012-04-05 11:53:47, product.tpl, ferz
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$extra_fields item=v}
  {if $v.active eq "Y" and $v.field_value}
    <tr>
      <td class="property-name property-name2">{$v.field}</td>
      <td class="property-value" colspan="2">{$v.field_value}</td>
    </tr>
  {/if}
{/foreach}
