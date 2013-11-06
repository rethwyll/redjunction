{*
f2c96ddb72c96605401a2025154fc219a84e9e75, v11 (xcart_4_6_1), 2013-08-19 12:16:49, jquery_ui.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{*jQuery UI Components included in jquery-ui.custom.min.js
    jquery.ui.core.min.js
    jquery.ui.widget.min.js
    jquery.ui.mouse.min.js
    jquery.ui.button.min.js
    jquery.ui.resizable.min.js
    jquery.ui.draggable.min.js
    jquery.ui.dialog.min.js
    jquery.ui.tabs.min.js
    jquery.ui.datepicker.min.js
    jquery.ui.position.min.js
*}
{load_defer file="lib/jqueryui/jquery-ui.custom.min.js" type="js"}
{if $usertype eq 'C'}
  {load_defer file="lib/jqueryui/jquery.ui.theme.css" type="css"}
{else}
  {load_defer file="lib/jqueryui/datepicker_i18n/jquery-ui-i18n.js" type="js"}
  {*Last loaded localization is default and used when $shop_language is not supported*}
  {load_defer file="lib/jqueryui/datepicker_i18n/jquery.ui.datepicker-en-GB.js" type="js"}
  {load_defer file="lib/jqueryui/jquery.ui.admin.css" type="css"}
{/if}
{load_defer file="css/jquery_ui.css" type="css"}
{if $config.UA.browser eq "MSIE" and $config.UA.version < 9}
{load_defer file="css/jquery_ui.IE8.css" type="css"}
{/if}
