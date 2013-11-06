{*
364899154e641eda732da9e52f32c39a60fe29b4, v5 (xcart_4_5_2), 2012-07-24 06:55:56, login_link.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}
<a href="{$authform_url}" title="{$lng.lbl_sign_in|escape}" {if not (($smarty.cookies.robot eq 'X-Cart Catalog Generator' and $smarty.cookies.is_robot eq 'Y') or ($config.Security.use_https_login eq 'Y' and not $is_https_zone))} onclick="javascript: return !popupOpen('login.php','', {ldelim}zIndex: 10001{rdelim});"{/if}{if $classname} class="{$classname|escape}"{/if} id="href_Sign_in">{$lng.lbl_sign_in}</a>
