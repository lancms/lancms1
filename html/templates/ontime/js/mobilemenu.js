

/**
 * Mobile menu
 */
(function($) {
    "use strict";

    function MobileMenu() {
    }

    MobileMenu.prototype = {

        open: function() {
            $("#mobilemenu").animate({left: 0});
            $("#page").animate({left:"80%"});
            $("body").addClass("sidebar-expanded");
        },

        close: function() {
            $("#mobilemenu").animate({left:"-80%"});
            $("#page").animate({left:0});
            $("body").removeClass("sidebar-expanded");
        },

        toggle: function() {
            var _ = this;
            if(parseInt($("#mobilemenu").css("left")) >= 0) {
                _.close();
            } else {
                _.open();
            }
        }

    };

    $.fn.mobileMenu = function () {
        return new MobileMenu();
    } 
})(jQuery);
