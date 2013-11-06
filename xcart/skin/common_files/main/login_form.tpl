{*
8faeb12f1c73aa112ed3404e046e0d7ce670780d, v9 (xcart_4_5_5), 2012-12-28 18:48:11, login_form.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<form action="{$authform_url}" method="post" name="loginform">
<input type="hidden" name="is_remember" value="{$is_remember}" />
<input type="hidden" name="mode" value="login" />

<table class="login-table">
<tr>
  <td colspan="3" class="login-title">{$login_title}</td>
</tr>

<tr> 
  <td class="data-name"><label for="username">{$login_field_name}</label></td>
  <td class="data-required">*</td>
  <td>
    <input type="text" name="username"{if $config.email_as_login eq 'Y'} class="input-email"{/if} id="username" size="30" value="{#default_login#|default:$username|escape}" />
  </td>
</tr>

<tr>
  <td class="data-name"><label for="password">{$lng.lbl_password}</label></td>
  <td class="data-required">*</td>
  <td><input type="password" name="password" id="password" size="30" maxlength="64" value="{#default_password#}" autocomplete="off" /></td>
</tr>

{if $active_modules.Image_Verification and $show_antibot.on_login eq 'Y' and $login_antibot_on and $main ne 'disabled'}
{include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_login}
<tr>
  <td colspan="3" class="ErrorMessage">{if $antibot_err}{$lng.msg_err_antibot}{/if}</td>
</tr>
{/if}

<tr> 
  <td colspan="2">&nbsp;</td>
  <td>
    <table width="100%">
      <tr>
        <td class="main-button">
          <button class="big-main-button" type="submit">{$lng.lbl_submit}</button>
        </td>
        <td><a href="help.php?section=Password_Recovery">{$lng.lbl_recover_password}</a></td>
      </tr>
    </table>
  </td>
</tr>
{if $is_register eq "Y"}

<tr class="register-row">
  <td colspan="2">&nbsp;</td>
  <td>
    <a href="register.php">{$lng.lbl_register}</a>
  </td>
</tr>
{/if}
</table>

</form>

