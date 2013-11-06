{*
2aca87f302048436ed08b4e6738089849840409f, v6 (xcart_4_5_3), 2012-08-07 09:50:06, authbox.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="login-text item right-box">
  <div class="register-button vertical-align">

{if $login ne ''}

  <strong>{$fullname|default:$login|escape}</strong>

  <a href="register.php?mode=update">{$lng.lbl_my_account}</a>&nbsp;&nbsp;
  <form action="login.php?mode=logout" method="post" name="loginform">
    <input type="hidden" name="mode" value="logout" />
    <a href="javascript:void(0);" onclick="javascript: setTimeout(function() {ldelim}document.loginform.submit();{rdelim}, 100);">{$lng.lbl_logoff}</a>
  </form>

  {if $active_modules.Quick_Reorder}
    {include file="modules/Quick_Reorder/quick_reorder_link.tpl" current_skin="fashion_mosaic"}
  {/if}

{else}
  
    {include file="customer/main/login_link.tpl"}&nbsp;&nbsp;
    <a href="register.php" title="{$lng.lbl_register|escape}">{$lng.lbl_register}</a>

{/if}

  </div>
</div>
