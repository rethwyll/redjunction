{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, acr_search_sort_by.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $sort_links}

  {foreach from=$sort_links key=name item=field name=sort_fields}
    <span class="acr-search-sort-cell-no-direction">
    {if $name eq $selected}
      <span class="acr-search-sort-selected">{$field|escape}</span>
    {else}
      <a href="{$url|amp}&amp;sort={$name|amp}" title="{$field|escape}" class="acr-search-sort-not-selected">{$field|escape}</a>
    {/if}
    </span>
    {if not $smarty.foreach.sort_fields.last}
      <span class="acr-search-sort-delimiter">|</span>
    {else}
      <span>&nbsp;</span>
    {/if}
  {/foreach}

{/if}
