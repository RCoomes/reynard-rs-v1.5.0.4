(function ($) {
    $.fn.showHide = function (options) {
        var defaults = {speed: 1000, easing: ''};
        var options = $.extend(defaults, options);
        $(this).click(function () {
            var toggleDiv = $(this).attr('rel');
            if ($(toggleDiv).is(":visible")) {
                $(this).children("#handle").addClass("plusIcon")
                $(this).children("#handle").removeClass("minusIcon")
            }
            else {
                $(this).children("#handle").addClass("minusIcon")
                $(this).children("#handle").removeClass("plusIcon")
            }
            $(toggleDiv).slideToggle(options.speed, options.easing);
            return false;
        });
    };
})(jQuery);

jQuery(document).ready(function(){
    jQuery('#tabs > div').hide();
    jQuery('#tabs > div:first').show();
    jQuery('#tabs ul li:first').addClass('active');

    jQuery('#tabs ul li a').click(function(){
        jQuery('#tabs ul li').removeClass('active');
        jQuery(this).parent().addClass('active');
        var currentTab = jQuery(this).attr('href');
        jQuery('#tabs > div').hide();
        jQuery(currentTab).show();
        return false;
    });
    jQuery('.show_hide').showHide({speed: 500, easing: ''});
});
