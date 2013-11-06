{*
fa050a8e6cb41e95e4cf7ba6d277c73c4fe23284, v2 (xcart_4_4_0), 2010-07-21 13:57:48, opc_form_field.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="field-container">
  <div class="data-name{if $oneline} oneline{/if}">
    {strip}
      <label {if $field ne ''}for="{$field}"{/if}{if $required eq 'Y'} class="data-required"{/if}>{$name}</label>
      {if $required eq 'Y'}<span class="star">*</span>{/if}
    {/strip}
  </div>

  <div class="data-value{if $oneline} oneline{/if}">
    {$content}
  </div>
  {if $oneline}
    <div class="clearing"></div>
  {/if}
</div>
