/* vim: set ts=2 sw=2 sts=2 et: */

$(function () {

  $(ajax.messages).bind(
    'productAdded',
    function(e, data) {

      $('.ui-dialog-content').dialog('close').dialog('destroy').remove();

      var dialog = $(data.content).not('script');
      var dialogScripts = $(data.content).filter('script');
      var is_mobile = ($(window).width() < 790);
      
      if (is_mobile) {
        var popup_width = 340;
        var popup_height = parseInt($(window).height() - 20);
      } else {
        var popup_width = 575;
        var popup_height = 'auto';
      }
      

      dialog.dialog({

        autoOpen: false,
        dialogClass: "product-added",
        modal: true,
        title: data.title,
        width: popup_width,
        height: popup_height,
        zIndex: 10000,
        resizable: false,
        //position: ["bottom", "bottom"],
        position: ["center", "center"],

        dragStart: function (event, ui) {
          $('.product-added').css('opacity', '0.7');
        },

        dragStop: function (event, ui) {
          $('.product-added').css('opacity', '1.0');
        },

        close: function() {
          dialogScripts.remove();
        },

        open: function () {
          $(".product-added .view-cart").button();
          $(".product-added .continue-shopping").button().click(function () {
            dialog.dialog('close');
            return false;
          });
          $(".product-added .proceed-to-checkout").button().click(function () {
            dialog.dialog('close');
          });
          $('.ui-widget-overlay').click(function () {
            dialog.dialog('close');
          });
        }

      });

      dialogScripts.appendTo('body');
      dialog.dialog('open');

      $('.ui-dialog a').blur();

      ajax.widgets.products();
    }
  );

});
