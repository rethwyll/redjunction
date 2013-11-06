{*
3ed6e3a60c9611d7e29d418bea33b85962aff70a, v2 (xcart_4_5_5), 2012-12-24 20:16:24, register.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $xauth_register_displayed}
  <tr class="xauth-register">
    <td class="data-name"><label>{$lng.lbl_xauth_your_identifiers}</label></td>
    <td>&nbsp;</td>
    <td>
      {if $xauth_ids}
        <ul>
          {foreach from=$xauth_ids key=k item=id}
            <li>
              <span>{$id.provider}-{$id.service}-{$id.auth_id}</span>
              <a class="remove" href="xauth_register.php?mode=remove&amp;auth_id={$id.auth_id}"></a>
            </li>
          {/foreach}
        </ul>
      {/if}
      <a href="javascript:void(0);" onclick="javascript: return xauthTogglePopup(this);">{$lng.lbl_xauth_add_identifier}</a>
    </td>
  </tr>
{/if}
{if $xauth_saved_data}
  <tr style="display: none">
    <td>
      <input type="hidden" name="xauth_identifier[service]" value="{$xauth_saved_data.service}" />
      <input type="hidden" name="xauth_identifier[provider]" value="{$xauth_saved_data.provider}" />
      <input type="hidden" name="xauth_identifier[identifier]" value="{$xauth_saved_data.identifier}" />
    </td>
  </tr>
{/if}
