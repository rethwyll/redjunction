{*
2aca87f302048436ed08b4e6738089849840409f, v1 (xcart_4_5_3), 2012-08-07 09:50:06, on_sale_product_modify_checkbox.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[on_sale]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_on_sale}:</td>
  <td class="ProductDetails">
    <input type="hidden" name="on_sale" value="N" />
    <input type="checkbox" name="on_sale" value="Y"{if $product.on_sale eq "Y"} checked="checked"{/if} />
  </td>
</tr>
