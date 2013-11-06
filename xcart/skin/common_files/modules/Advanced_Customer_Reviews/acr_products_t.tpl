{*
31c916a86f77bd691d94210afd54135cd7103170, v3 (xcart_4_5_3), 2012-10-02 07:29:56, acr_products_t.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<tr{interline name=products_matrix}>

  {foreach from=$row item=product name=products}

    {if $product}
      <td{interline name=products additional_class="product-cell"}>
      {if $product.general_rating}
        {include file="modules/Advanced_Customer_Reviews/general_product_rating.tpl" general_rating=$product.general_rating productid=$product.productid is_multicolumns=$is_multicolumn}
        {if $break_line eq "Y"}
          <br />
        {/if}
      {/if}
      </td>

      {if $column_separator eq "Y"}
        {if !$smarty.foreach.products.last}
          <td class="column_separator"><div>&nbsp;</div></td>
        {/if}
      {/if}

    {/if}
  {/foreach}
</tr>
