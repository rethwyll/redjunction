{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, usps.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
<form action="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do" method="post" name="getTrackNum" id="getTrackNum" target="_blank">
<input type="hidden" id="strOrigTrackNum" name="strOrigTrackNum" value="{$order.tracking|escape}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_usps_redirection}
</form>
