$(document).ready(function() {
    $("#content-slider").lightSlider({
        autoWidth: true,
        loop: true,
        auto: true,
        speed: 500,
        slideMargin: 5,
        slideMove: 1,
        enableDrag: true,
        enableTouch: true,
        pauseOnHover: true,
        pager: false,
        onAfterSlide: function () {
            $('ul.lSPager li').each(function () {
                if (!$(this).hasClass('active')) {
                    $(this).find('a').css('background-color', '#222222');
                } else {
                    $(this).find('a').css('background-color', '#428bca');
                }
            });
        }
    });

});