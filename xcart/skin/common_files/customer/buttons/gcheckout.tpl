{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, gcheckout.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
<input type="image" name="{$lng.lbl_google_checkout}" alt="" src="http://checkout.google.com/buttons/checkout.gif?merchant_id={$payment_data.module_params.param01}&amp;w=160&amp;h=43&amp;style=white&amp;variant=text&amp;loc=en_US" height="43" width="160"{if $onclick} onclick="{$onclick}"{/if} />
