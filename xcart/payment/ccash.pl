#!/usr/bin/perl

#
# 20e220d575ec68b25329a62122c50c7686c91522, v6 (xcart_4_3_0_beta_1), 2009-04-09 07:14:57, ccash.pl, joy
#

BEGIN
{
# !!! IMPORTANT !!!
# Path to mck-cgi dir. For example: /mystore/merchantid/mck-cgi
$path2mck = "/path-to-mck/login/mck-cgi";
push(@INC,$path2mck);
}

# MCK function
use CCMckLib3_2 qw(InitConfig);
use CCMckDirectLib3_2 qw(SendCC2_1Server doDirectPayment);
use CCMerchantTest qw($ConfigFile);
use CCMerchantCustom qw(GenerateOrderId);

exit if (!($#ARGV+1)); 

foreach $item (@ARGV)
{
	$key="";
	$val="";
	($key,$val)=split('=',$item,2);
	if ($key ne "")
	{
		$val=~ s/\"//; 
		$args{$key}=$val;
	}
}

$status= &InitConfig ($ConfigFile);
if($status)
	{ print "2,,Unable to initialize configuration";exit;}

%result = &SendCC2_1Server ('mauthcapture', %args);

if (($result{'MStatus'} ne "success") && ($result {'MStatus'} ne "success-duplicate"))
	{ print "2,,".$result{'MErrMsg'}." (ErrCode: ".$result{'MErrCode'}.")"; }
else
	{ print "1,".$result{'avs-code'}.",AuthCode: ".$result{'auth-code'}."; ActionCode: ".$result{'action-code'}."; RefCode: ".$result{'ref-code'};}
