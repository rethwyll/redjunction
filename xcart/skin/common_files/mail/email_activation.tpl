{*
dc21e5a8162683b211ca53dc1512dab280748457, v2 (xcart_4_5_5), 2013-01-16 17:52:17, email_activation.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}
 
{$lng.eml_email_activation|substitute:"login_name":$userinfo.login}:

{$http_location}/login.php?activation_key={$activation_key}

{include file="mail/signature.tpl"}
