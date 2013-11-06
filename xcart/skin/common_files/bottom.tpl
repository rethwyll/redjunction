{*
22f6923a9e65090657fef8e74ebb5436d9e861ee, v9 (xcart_4_6_0), 2013-05-23 14:12:07, bottom.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%" cellpadding="0" cellspacing="0">

<tr>
  <td>
  <table width="100%">
    <tr>

      {if $active_modules.Users_online ne ""}
      <td class="users-online-box">
        {include file="modules/Users_online/menu_users_online.tpl"}
      </td>
      {/if}

      {if $login}
      <td class="store-open-close-link">
        {include file="storefront_status.tpl" no_container=true}
      </td>
      {else}
        <td>&nbsp;</td>
      {/if}

      {if $login and $all_languages_cnt gt 1}
      <td class="admin-language">
        <form action="{$smarty.server.REQUEST_URI|escape}" method="post" name="asl_form">
          <input type="hidden" name="redirect" value="{$php_url.query_string|escape}" />
          {$lng.lbl_language}:
          <select name="asl" onchange="javascript: document.asl_form.submit()">
          {foreach from=$all_languages item=l}
          <option value="{$l.code}"{if $current_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
          {/foreach}
          </select>
        </form>
      </td>
      {/if}

      </tr>
    </table>
  </td>
</tr>

<tr>
  <td class="HeadThinLine">
    <img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" />
  </td>
</tr>

<tr>
  <td class="BottomBox">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="Bottom" align="left">
          {include file="main/prnotice.tpl"}
        </td>
        <td class="Bottom" align="right">
          {include file="copyright.tpl"}
        </td>
      </tr>
    </table>
  </td>
</tr>

</table>
