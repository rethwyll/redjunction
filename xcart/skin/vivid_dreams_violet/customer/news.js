/*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, news.js, joy
vim: set ts=2 sw=2 sts=2 et:
*/
$(document).ready(
  function() {
    $('#show').css('display', '');
    $('#show2').css('display', '');
    $('#subscribe').hide();		
    $('#title2').hide();				
    $('#show').click(
      function() {				
        $('#news').hide("fast");
        $('#subscribe').show("fast");
        $('#news_title').css('display','none');
        $('#title2').show('fast');								
        return false;
      }
    );
    $('#show2').click (
      function() {
        $('#news').show("fast");
        $('#subscribe').hide("fast");
        $('#title2').css('display','none');
        $('#news_title').show('fast');	
        return false;
      }
    )
  }
);
