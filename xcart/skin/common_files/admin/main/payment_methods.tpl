{*
0cd6aad2baf5c043df8cab7e5080a1c389009774, v9 (xcart_4_6_1), 2013-07-11 13:41:57, payment_methods.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="page_title.tpl" title=$lng.lbl_payment_methods}

{include file="customer/main/ui_tabs.tpl" prefix="payment-tabs-" mode="inline" tabs=$payment_methods_tabs}

<script type="text/javascript" src="{$SkinDir}/admin/js/payment_methods.js"></script>
