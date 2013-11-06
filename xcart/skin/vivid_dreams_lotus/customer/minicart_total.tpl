{*
1433d47b92a58a0f7a03bd15ff0d3516bd8dc8ad, v4 (xcart_4_4_5), 2011-11-10 09:20:44, minicart_total.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="minicart">
  {if $minicart_total_items gt 0}

    <div class="valign-middle full">
      {capture name=tt assign=val}
        {currency value=$minicart_total_cost}
      {/capture}
      <span class="minicart-text">
        <strong>{$minicart_total_items}</strong> {$lng.lbl_cart_items}&nbsp;/&nbsp;<strong class="help-link">{include file="main/tooltip_js.tpl" class="help-link" title=$val text=$lng.txt_minicart_total_note}</strong>
      </span>
    </div>

  {else}

    <div class="valign-middle empty">

      <strong>{$lng.lbl_cart_is_empty}</strong>

    </div>

  {/if}

{if $minicart_total_standalone}
{load_defer_code type="css"}
{load_defer_code type="js"}
{/if}
</div>
