{*
553ad884cebe25b494a85fd5177abb3f8ddf289e, v1 (xcart_4_5_3), 2012-08-15 07:26:38, product_notification.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p>{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}</p>

{assign var=notification_tpl_name value="mail/html/product_notification_`$type`.tpl"}
{include file=$notification_tpl_name}

{assign var=config_unsubscribe_flag_name value="prod_notif_send_unsub_link_`$type`"}
{if $config.Product_Notifications.$config_unsubscribe_flag_name eq 'Y'}
{$lng.eml_prod_notif_unsubscribe|substitute:"url":$notification_data.unsubscribe_url}
{/if}

{include file="mail/html/signature.tpl"}
