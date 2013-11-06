{*
c2c354cc873d4145a4a1740ba5384cf57a3b0a3a, v7 (xcart_4_5_1), 2012-06-04 06:52:43, ui_tabs.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
$(function() {ldelim}
  var days = ('function' != typeof window.func_is_allowed_cookie || func_is_allowed_cookie('ui-tabs-')) ? 1 : -1;
  var tOpts = {ldelim}
    idPrefix: '{$prefix|default:"ui-tabs-"}', cookie: {ldelim} expires: days {rdelim}{if $selected}, selected: '{$selected}'{/if}
  {rdelim};
  $('#{$prefix}container').tabs(tOpts);
{rdelim});
//]]>
</script>

<div id="{$prefix}container">

  <ul>
  {foreach from=$tabs item=tab key=ind}
    {inc value=$ind assign="ti"}
    <li><a href="{if $tab.url}{$tab.url|amp}{else}#{$prefix}{$tab.anchor|default:$ti}{/if}">{$tab.title|wm_remove|escape}</a></li>
  {/foreach}
  </ul>

  {foreach from=$tabs item=tab key=ind}
    {if $tab.tpl}
      {inc value=$ind assign="ti"}
      <div id="{$prefix}{$tab.anchor|default:$ti}">
        {include file=$tab.tpl nodialog=true}
      </div>
    {/if}
  {/foreach}

</div>
