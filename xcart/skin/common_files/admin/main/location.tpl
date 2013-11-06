{*
200d2f201327da5d0f79081817f361f72eebdde9, v3 (xcart_4_4_6), 2012-03-27 06:21:45, location.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $category_location and $cat ne ""}
<div class="navigation-path">
{strip}
{section name=position loop=$category_location}
  {if $category_location[position].1 ne ''}
    {if %position.last%}
      <span class="current">
    {else}
      <a href="{$category_location[position].1|amp}">
    {/if}
  {/if}
  {$category_location[position].0}
  {if $category_location[position].1 ne ''}
    {if %position.last%}</span>{else}</a>{/if}
  {/if}
  {if %position.last% ne "true"}&nbsp;/&nbsp;{/if}
{/section}
</div>
{/strip}
<br /><br />
{/if}
