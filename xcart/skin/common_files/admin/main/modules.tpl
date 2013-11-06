{*
704c128b5fd0a72faa110932eb9a8f8e5c6700bd, v9 (xcart_4_6_0), 2013-03-27 13:00:49, modules.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_modules}

{*
{$lng.txt_modules_top_text}

<br /><br />
*}
{include file="customer/main/ui_tabs.tpl" prefix="modules-tabs-" mode="inline" tabs=$modules_tabs}
