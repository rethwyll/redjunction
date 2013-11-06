{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, review_status.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $static eq 'Y'}
{if $statuses}{foreach from=$statuses item=v key=status}{if $review_status eq $status}{$v}{/if}{/foreach}{/if}
{else}

{if $field eq ''}
  {assign var="field" value="status"}
{/if}
{assign var="size" value=1}

{if $statuses}
  {count assign="size" value=$statuses print=false}
  {inc assign="size" value=$size}

  {if $size gt 5}
    {assign var="size" value=5}
  {/if}

{/if}

<select name="{$field}"{if $is_short ne 'Y'} multiple="multiple" size="{$size}"{/if}>
  {if $statuses}
    {foreach from=$statuses item=v key=status}
      <option value="{$status}"{if ($data neq "" and $data[$status] ne '') || $review_status eq $status} selected="selected"{/if}>{$v|escape}</option>
    {/foreach}

  {/if}

</select>
{if $is_short ne 'Y'}
  <p>{$lng.lbl_hold_ctrl_key}</p>
{/if}
{/if}
