{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, title_selector.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
<select name="{$name|default:"title"}" id="{$id|default:"title"}">
{if $titles}
{foreach from=$titles item=v}
  <option value="{if $use_title_id eq "Y"}{$v.titleid}{else}{$v.title_orig|escape}{/if}"{if $val eq $v.titleid} selected="selected"{/if}>{$v.title}</option>
{/foreach}
{else}
  <option value="{if $use_title_id eq "Y"}{$val}{/if}" selected="selected">{$lng.txt_no_titles_defined}</option>
{/if}
</select>
