{*
7d40df4393852127c0e1fd9b26ae8446566685fc, v1 (xcart_4_5_5), 2012-12-20 13:36:04, new_arrivals_product_modify_date.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $product.add_date gt 0}
  <br />
  <div>
    <span class="FormButton">{$lng.lbl_added}:</span>&nbsp;{$product.add_date|date_format:$config.Appearance.date_format}
  </div>
{/if}
