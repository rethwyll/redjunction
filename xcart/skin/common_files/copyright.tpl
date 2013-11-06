{*
bac94d5eb626f862c328fe22ba9754042daa8269, v3 (xcart_4_4_4), 2011-09-16 14:53:25, copyright.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.lbl_copyright} &copy; {$config.Company.start_year}{if $config.Company.start_year lt $config.Company.end_year}-{$smarty.now|date_format:"%Y"}{/if} {$config.Company.company_name|escape}

{if $active_modules.Socialize}
  {include file="modules/Socialize/footer_links.tpl"}
{/if}
