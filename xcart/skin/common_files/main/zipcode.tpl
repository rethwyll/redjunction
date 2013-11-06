{*
5ea8558546dbb610189b4750a0db31b66017f544, v5 (xcart_4_4_5), 2011-12-15 12:28:40, zipcode.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}
{if not $static}

  {assign var=cntid value=$id|regex_replace:'/zipcode/':'country'|escape}
  <input type="text" id="{$id|escape}" class="zipcode{if $zip_section} {$zip_section}{/if}" name="{$name|escape}" size="32" maxlength="32" value="{$val|escape}" />
  {if $config.General.zip4_support eq 'Y' and not $nozip4}
  {strip}
    {assign var=zip4id value=$id|regex_replace:'/zipcode/':'zip4'|escape}
    {assign var=zip4name value=$name|regex_replace:'/zipcode/':'zip4'|escape}
    <span id="{$zip4id}_container">
      &nbsp;-&nbsp;
      <input type="text" id="{$zip4id}" class="zip4" name="{$zip4name}" size="10" maxlength="4" value="{$zip4|escape}" />
    </span>
  {/strip}
  {/if}

{else}
{if $is_csv_export eq 'Y'}
{$val}{if $zip4 ne ''}-{$zip4}{/if}
{else}
{$val|escape:"html"}{if $zip4 ne ''}-{$zip4|escape:"html"}{/if}
{/if}
{/if}
