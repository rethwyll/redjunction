{*
1bc598fc2f0da85d9d5315fbf81bc3d2fed689a3, v2 (xcart_4_6_0), 2013-02-18 18:19:13, button_add_review.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $add_review_status.is_allow_add_review}
{include file="customer/buttons/button.tpl" button_title=$lng.lbl_acr_add_review href="add_review.php?productid=`$productid`"}
<br />
{else}
  <span class="acr-reason">

  {* Define behaviour of login.php link *}
  {if not (($smarty.cookies.robot eq 'X-Cart Catalog Generator' and $smarty.cookies.is_robot eq 'Y') or ($config.Security.use_https_login eq 'Y' and not $is_https_zone))} 
    {capture name='login_link'}
 onclick="javascript: return !popupOpen('login.php', '',{ldelim}zIndex: 10001{rdelim});"
    {/capture}
  {else}
    {capture name='login_link'} {/capture}
  {/if}

	{if $add_review_status.reason eq 'reviewed'}
		{$lng.lbl_acr_you_already_reviewed}
	{elseif $add_review_status.reason eq 'need_login'}
    {$lng.lbl_acr_need_login|substitute:href:$authform_url|substitute:additional:$smarty.capture.login_link}
	{elseif $add_review_status.reason eq 'need_purchase'}
	  {$lng.lbl_acr_need_purchase}	
		{if $login eq ''}
      <br />
      {$lng.lbl_acr_need_purchase_login|substitute:href:$authform_url|substitute:additional:$smarty.capture.login_link}
		{/if}
	{/if}
  </span>
{/if}
