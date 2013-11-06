{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, spambot_requirements.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
{if $spambot_requirements ne ''}

<br />

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_issues_found class="grey"}
{$spambot_requirements}
{/if}
