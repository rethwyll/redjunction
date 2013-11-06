{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, rating_selector.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $field eq ''}
  {assign var="field" value="rating[]"}
{/if}
{assign var="size" value=1}

{if $stars}
  {count assign="size" value=$stars.levels print=false}
  {inc assign="size" value=$size}

  {if $size gt 6}
    {assign var="size" value=6}
  {/if}

{/if}

<select name="{$field}"{if $is_short ne 'Y'} multiple="multiple" size="{$size}"{/if}>
  <option value="0"{if ($data neq "" and $data[0] eq '0') || $review_rating eq '0'} selected="selected"{/if}>0-{$lng.lbl_acr_star}</option>

  {if $stars}
    {foreach from=$stars.levels item=v key=level}
      <option value="{$v}"{if ($data neq "" and $data[$v] ne '') || $review_rating eq $v} selected="selected"{/if}>{$level+1}-{$lng.lbl_acr_star}</option>
    {/foreach}
  {/if}
</select>
{if $is_short ne 'Y'}
  <p>{$lng.lbl_hold_ctrl_key}</p>
{/if}
