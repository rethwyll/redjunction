{*
10d4766a297b130dea8de7e1d6cd01925e213749, v1 (xcart_4_6_0), 2013-04-09 11:07:38, service_css.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.UA.browser eq 'MSIE'}
  {assign var=ie_ver value=$config.UA.version|string_format:'%d'}
{/if}
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/admin.css" />
{if $ie_ver ne ''}
<style type="text/css">
<!--
{/if}
{strip}
{foreach from=$css_files item=files key=mname}
  {foreach from=$files item=f}
    {if $f.admin}
      {if not $ie_ver}
        <link rel="stylesheet" type="text/css" href="{$SkinDir}/modules/{$mname}/{$f.subpath}admin{if $f.suffix}.{$f.suffix}{/if}.css" />
      {else}
        @import url("{$SkinDir}/modules/{$mname}/{$f.subpath}admin{if $f.suffix}.{$f.suffix}{/if}.css");
      {/if}
    {/if}
  {/foreach}
{/foreach}
{/strip}
{if $ie_ver ne ''}
-->
</style>
{/if}
