{*
f2c96ddb72c96605401a2025154fc219a84e9e75, v9 (xcart_4_6_1), 2013-08-19 12:16:49, location.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $location ne ""}

<div id="location">
{strip}
{section name=position loop=$location}
{if $location[position].1 ne ""}
  <a href="{$location[position].1|amp}">
{/if}
    <span>{$location[position].0|amp}</span>
{if $location[position].1 ne ""}
  </a>
{/if}
{if not %position.last%}
&nbsp;{$config.Appearance.breadcrumbs_separator|amp}&nbsp;
{/if}
{/section}
{/strip}
</div>

{/if}

<!-- check javascript availability -->
<noscript>
  <table width="500" cellpadding="2" cellspacing="0" align="center">
  <tr>
    <td align="center" class="ErrorMessage">{$lng.txt_noscript_warning}</td>
  </tr>
  </table>
</noscript>

{if $alt_content}
<table id="{$newid|default:"dialog-message"}" width="100%">
<tr>
  <td>
    <div class="dialog-message">
      <div class="box message-{$alt_type|default:"I"}">

        <table width="100%">
        <tr>
{if $image_none ne "Y"}
          <td width="50" valign="top">
            <img class="dialog-img" src="{$ImagesDir}/spacer.gif" alt="" />
          </td>
{/if}
          <td align="left" valign="top">
            {$alt_content}
          </td>
        </tr>
        </table>
      </div>
    </div>
  </td>
</tr>
</table>
{elseif $top_message}
  {include file="main/top_message.tpl"}
{/if}
