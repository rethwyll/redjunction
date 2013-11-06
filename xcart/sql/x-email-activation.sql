INSERT INTO xcart_modules SET module_name='Email_Activation', module_descr='This module enable email activation for new customer profiles', active='N', init_orderby=0, author='qualiteam', module_url='', tags='security';

INSERT INTO xcart_config SET name='email_activation_usertypes', comment='Applicable usertypes', value='C', category='Email_Activation', orderby='10', type='multiselector', defvalue='', variants='func_get_active_usertypes', validation='';
INSERT INTO xcart_config SET name='signin_notif_after_activation', comment='Do not send \'Profile is created\' notifications to profile owners before account activation', value='Y', category='Email_Activation', orderby='20', type='checkbox', defvalue='Y', variants='', validation='';
