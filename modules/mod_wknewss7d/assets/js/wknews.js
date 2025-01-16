/*
    News
*/

(function( $ ) {
    $.fn.wknews = function( opts ){
        var wkpdefault = $.extend({
            modId : ''

        },opts);

        $( document ).on('owlNav',function(){

            var hBimg = $('.wk-news-slider-'+opts.modId+'').find('.wk-news-img').outerHeight();
            var hOnav = $('.wk-news-slider-'+opts.modId).find('.owl-nav').outerHeight();

            var topNav = (hBimg - hOnav) / 2;

            $('.wk-news-slider-'+opts.modId).find('.owl-nav').css({
            	"top": topNav,
            	"opacity": 1
            }); 
        });

        $(document).trigger('owlNav');

        $( window ).resize(function(){
            $(document).trigger('owlNav');
        });

    };
}(jQuery));