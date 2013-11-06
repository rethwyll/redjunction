REPLACE INTO xcart_languages SET code='en', name='module_descr_XAuth', value='This module enables Janrain authentication', topic='Modules';
REPLACE INTO xcart_languages SET code='en', name='module_name_XAuth', value='Social Login', topic='Modules';

REPLACE INTO xcart_languages SET code='en', name='opt_xauth_create_profile', value='Create profile', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_auto_login', value='Auto login', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_login_by_email', value='Login by email', topic='Options';

REPLACE INTO xcart_languages SET code='en', name='opt_xauth_sep1', value='Social Login (RPX) options', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_rpx_api_key', value='API key', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_rpx_app_id', value='Application ID', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_enable_social_sharing', value='Enabled Social Sharing button on Product page', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_enable_ss_cart', value='Enabled Social Sharing button on Cart page', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_enable_ss_invoice', value='Enabled Social Sharing button on Invoice page', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_descr_xauth_enable_social_sharing', value='Enable users to share content and activities from your site to their friends on multiple social networks and grow referral traffic', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_rpx_app_name', value='Application name', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_rpx_services', value='Enabled services', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_descr_xauth_rpx_services', value='', topic='Options';
REPLACE INTO xcart_languages SET code='en', name='opt_xauth_rpx_display_mode', value='Display mode', topic='Options';


REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_open', value='Login via ...', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_user_cannot_create_email', value='User cannot be created because some required fields are empty or missing', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_user_cannot_create_email_duplicate', value='User cannot be created because a user with this email already exists', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_user_cannot_create_username_duplicate', value='User cannot be created because a user with this username already exists', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_error', value='An auth_info transaction of the Social Login (formerly RPX) service was unsuccessful. The server returned an error {{message}} with error code {{code}}', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_parse_error', value='An auth_info transaction of the Social Login (formerly RPX) service was unsuccessful. The server response is missing or cannot be considered valid', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_login_failed', value='You have not signed in. Try to use another authentication method.', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_openid', value='OpenID', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_your_identifiers', value='Your identifiers', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_add_identifier', value='Add an identifier', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_fill_account_from_ext', value='Fill account from external source', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_profile_is_incompleted', value='You have to complete registration by filling necessary fields', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_checkout_link', value='If you wish to create account from external profile (qooqle, twitter, facebook etc) <a href="javascript:void(0);" onclick="javascript: return xauthTogglePopup(this, \'fill\');">click here</a>', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_forbid_login_note', value='You can\'t use this identifier because it is used for another x-cart profile (email). If you want to use this identifier with current account, you should sign in with (email) and delete relation on Manage account page. After that you will be able to connect current account with this identifier', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_delay_login_note', value='Your account has been created using the data from an external source, but the site policy prohibits automatic sign in after account creation. Please sign in again using the same external source', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_register_link', value='Sign in one of these accounts {{accounts}} or fill you personal info', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_opc_sign_in', value='If you already have an account please {{sign_in_link}} or sign in using one of these accounts {{accounts}}', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_can_not_link', value='Identifier can not link to account. Try to link another identifier method.', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_link_email_note', value='<span class="xauth-err">You logged in to this account because the email registered for the the account and the email provided by the external authentication source have matched. This indicates that an account with the email used in the external authentication source had been registered in the system. If neither you nor your authorized representative created an account on this site using the email {{email}}, either delete this account or log out and avoid using it. Please also report this issue to the administrator of the website.</span>', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_share_comment', value='Share comment', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_look_this_product', value='Look at this product {{product}} on {{site}}', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_share', value='Share', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_share_and_more', value='and more', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_share_and_more_invoice', value='', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_rpx_invoice_share_note', value='I\'ve just placed an order at {{site}}', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_display_vertical', value='Vertical', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_display_horizontal', value='Horizontal', topic='Labels';

REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_login_disabled_note', value='Unable to authenticate, as the bound user profile is disabled or was created anonymously. Try using other external identifier.', topic='Labels';
REPLACE INTO xcart_languages SET code='en', name='lbl_xauth_email_is_multiple', value='Your identifier is bound to an e-mail used in several accounts. Please login under an account with this e-mail or create an account with a new e-mail.', topic='Labels';

