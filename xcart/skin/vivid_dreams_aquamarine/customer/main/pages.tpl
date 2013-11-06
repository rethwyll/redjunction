{*
93fa73dc2a71d6ab2047baa83e8549142dc9a848, v2 (xcart_4_4_0), 2010-07-22 09:21:34, pages.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$page_data.title|escape}</h1>

{capture name=dialog}

  {if $page_content ne ''}

    {if $config.General.parse_smarty_tags eq "Y"}
      {eval var=$page_content}
    {else}
      {$page_content}
    {/if}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$page_data.title content=$smarty.capture.dialog noborder=true additional_class="big_title"}
