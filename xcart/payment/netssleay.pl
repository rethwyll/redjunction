#!/usr/bin/perl
#
# 67387eec54eee3944fb1575e9dc276621de3b476, v15 (xcart_4_3_0_beta_1), 2009-03-19 06:20:21, netssleay.pl, max
#

require Net::SSLeay;
Net::SSLeay->import ( qw(sslcat));
$Net::SSLeay::slowly = 5; # Add sleep so broken servers can keep up

if ($#ARGV < 1) {
 	print <<EOF;
 Usage: $0 host port use_ssl3 [cert [keycert]] < requestfile
EOF
	exit;
}

($host, $port, $use_ssl3, $cert, $kcert) = @ARGV;

if ($use_ssl3 == '1') {
	$Net::SSLeay::ssl_version = 3;
}

$request = "";
while(<STDIN>) {
	$request .= $_;
}

($reply) = sslcat($host, $port, $request, $cert, $kcert);
print $reply;

# tested revision: 1.1; 1.4.9; 1.9.7; 1.5.0; 1.9.8; 1.5.1; 1.9.9.release
