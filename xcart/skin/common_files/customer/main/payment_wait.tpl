{*
1e1924d979329a127f421a68658195f4e14e6c8e, v6 (xcart_4_6_0), 2013-04-09 12:52:17, payment_wait.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<?xml version="1.0" encoding="{$default_charset|default:"utf-8"}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.msg_order_is_being_placed|wm_remove|escape}</title>
  {include file="customer/meta.tpl"}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.css" type="css"}

{if $use_iframe eq 'Y'}
  {load_defer file="lib/jquery-min.js" type="js"}
  {load_defer file="js/ajax.js" type="js"}
{/if}

{if $AltSkinDir}
  {load_defer file="css/altskin.css" type="css"}
  {if $config.UA.browser eq "MSIE"}
    {load_defer file="css/altskin.IE`$ie_ver`.css" type="css"}
  {/if}
{/if}

{load_defer_code type="css"}
{load_defer_code type="js"}

</head>
<body{$reading_direction_tag} class="payment-wait">

<div class="payment-wait-title">
  <h1>{$lng.msg_order_is_being_placed}</h1>
  <img src="{$ImagesDir}/spacer.gif" class="payment-wait-image" alt="" />
</div>
