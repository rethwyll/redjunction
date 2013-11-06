{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, permanent_warning.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
{if $permanent_warning}
{strip}
<ol class="pw">
{foreach from=$permanent_warning item=pw key=k}
<li{if $k eq 0} class="first-child"{/if}>{$pw}</li>
{/foreach}
</ol>
{/strip}
{/if}
