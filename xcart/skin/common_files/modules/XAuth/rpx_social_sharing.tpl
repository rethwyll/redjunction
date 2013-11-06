{*
4f1ff6c4e9836a1056434e21838988bb60aaef4d, v2 (xcart_4_6_0), 2013-04-30 17:31:58, rpx_social_sharing.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
<!--
var xauth_rpx_app_id = '{$config.XAuth.xauth_rpx_app_id}';
var xauth_rpx_language = '{xauth_rpx_get_language}';
var xauth_http_protocol = '{$xauth_http_protocol}';
var lbl_xauth_rpx_share_comment = "{$lng.lbl_xauth_rpx_share_comment|escape:javascript}";
var lbl_xauth_rpx_look_this_product = "{$lng.lbl_xauth_rpx_look_this_product|substitute:site:$config.Company.company_website|escape:javascript}";
var lbl_xauth_rpx_invoice_share_note = "{$lng.lbl_xauth_rpx_invoice_share_note|substitute:site:$config.Company.company_website|escape:javascript}";
var xauth_catalogs_customer = "{$catalogs.customer|wm_remove|escape:javascript}";
var xauth_current_host = "{$xauth_current_host|wm_remove|escape:javascript}";
var xauth_rpx_app_name = '{$config.XAuth.xauth_rpx_app_name}';
{literal}
(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
    if (typeof window.janrain.settings.share !== 'object') window.janrain.settings.share = {};
    if (typeof window.janrain.settings.packages !== 'object') janrain.settings.packages = [];
    janrain.settings.packages.push('share');

    janrain.settings.share.message = "";
    janrain.settings.share.title = lbl_xauth_rpx_share_comment;
    janrain.settings.share.url = self.location;
    janrain.settings.share.description = "";

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
        document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
        window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainWidgets';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/' + xauth_rpx_app_name + '/widget.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/' + xauth_rpx_app_name + '/widget.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();
{/literal}
-->
</script>
