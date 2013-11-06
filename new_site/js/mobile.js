$(document).ready(function () {
	if (window.rj.currentContext == 'phone') {
		var b = document.getElementsByTagName('body')[0];         
		var s = document.createElement('script');
		s.type = 'text/javascript';
		s.src = 'http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js';
		b.appendChild(s); 		
	}
});