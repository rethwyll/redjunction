{*
0cbfececc6d85ed304deef99a02e5b743bf42c0d, v10 (xcart_4_4_5), 2012-01-30 11:01:26, address_book_link.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_areas.B}
<div class="address-book-link">
  {if $change_mode eq 'Y'}
    <label class="save-new" for="new_{$type}">
      <input type="checkbox" name="new_address[{$type}]" id="new_{$type}" value="{$addressid}" onclick="javascript: if (this.checked) $('#existing_{$type}').prop('checked', false);" />
      {$lng.lbl_save_as_new_address}
    </label>
    {if $addressid gt 0}
    <br />
    <label class="update-existing" for="existing_{$type}">
      <input type="checkbox" name="existing_address[{$type}]" id="existing_{$type}" value="{$addressid}" onclick="javascript:  if (this.checked) $('#new_{$type}').prop('checked', false); " checked="checked" />
      {$lng.lbl_update_existing_address}
    </label>
    {/if}
  {/if}
  {if $hide_address_book_link ne 'Y'}
  <span class="popup-link">
    <a href="popup_address.php?mode=select&amp;for=cart&amp;type={$type}" onclick="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type={$type|escape:"javascript"}'); return false;" title="{$lng.lbl_address_book|escape}">{$lng.lbl_address_book}</a>
  </span>
  {/if}
  <div class="clearing"></div>
</div>
{/if}
