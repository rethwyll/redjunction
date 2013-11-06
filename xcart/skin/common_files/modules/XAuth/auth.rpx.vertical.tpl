{*
b382480f14e40f3e5752ca28d075b16f973ec050, v1 (xcart_4_5_3), 2012-08-08 14:19:27, auth.rpx.vertical.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" class="xauth-auth xauth-auth-rpx xauth-vertical">
  <tr>
    <td class="xauth-popup xauth-popup-rpx">
      <div class="xauth-overlay">
        <div class="xauth-bg-loading">{$lng.lbl_loading}</div>
        <iframe src="{$xauth_http_protocol}://{$config.XAuth.xauth_rpx_app_name}.rpxnow.com/openid/embed?token_url={$xauth_rpc_token_url}&amp;language_preference={xauth_rpx_get_language}" scrolling="no" frameBorder="no"></iframe>
      </div>
    </td>
  </tr>
  <tr>
    <td class="xauth-or">{$lng.lbl_or}</td>
  </tr>
  <tr>
    <td class="xauth-form">{include file=$xauth_rpx_auth_template}</td>
  </tr>
</table>
