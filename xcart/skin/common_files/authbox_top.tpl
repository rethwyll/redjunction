{*
444c1e1807f531a36170c0e65e6f5fb1bc369273, v11 (xcart_4_6_0), 2013-02-19 16:13:38, authbox_top.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="2" cellspacing="0" border="0">
<tr>

  {if $login ne '' and $usertype eq 'B'}
    <td nowrap="nowrap" height="20" valign="top" class="partnerid-info">
      {$lng.lbl_your_partner_id}: <strong>{$logged_userid}</strong>
    </td>
  {/if}

  <td class="AuthText" height="20" valign="top">
    <a href="{$current_area}/register.php?mode=update">{$fullname}</a>
  </td>

  <td valign="top" class="auth-text-wrapper">
    [ <a href="login.php?mode=logout" class="AuthText">{$lng.lbl_logoff}</a> ]
  </td>

  {if $need_quick_search eq "Y"}

    <td width="50">&nbsp;</td>

    <td class="quick-search-form" valign="top">
      <form name="qsform" action="" onsubmit="javascript: quick_search($('#quick_search_query').val()); return false;">
        <input type="text" class="default-value" id="quick_search_query" onkeypress="javascript:$('#quick_search_panel').hide();" onclick="javascript:$('#quick_search_panel').hide();" value="{$lng.lbl_keywords|escape}" />
      </form>
    </td>

    <td class="main-button" nowrap="nowrap">
      <button class="quick-search-button" onclick="javascript:quick_search($('#quick_search_query').val());return false;">{$lng.lbl_search}</button>

      {include file="main/tooltip_js.tpl" text=$lng.txt_how_quick_search_works id="qs_help" type="img" sticky=true alt_image="question_gray.png" wrapper_tag="div"}
    </td>
{/if}

</tr>
</table>
